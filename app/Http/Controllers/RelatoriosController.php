<?php

namespace App\Http\Controllers;

use App\Models\NotaFiscal;
use App\Support\Formatters;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Http\ResponseFactory;

class RelatoriosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response|ResponseFactory
     */
    public function porCliente(Request $request)
    {
        $this->validate($request, [
            'cliente_id' => 'required|integer|exists:clientes,id',
            'empresas' => 'nullable|array',
            'empresas.*' => 'integer|exists:empresas,id',
            'status' => 'required|in:TODAS,PENDENTE,DEVOLVIDAS',
            'datas.inicio' => 'required|date',
            'datas.fim' => 'required|date|after_or_equal:datas.inicio',
        ]);

        $query = NotaFiscal::query()
            ->with([
                'cliente',
                'empresa',
                'itens.material',
            ])
            ->where('cliente_id', $request->cliente_id)
            ->whereBetween('emissao', [
                $request->datas['inicio'],
                $request->datas['fim'],
            ]);

        if (!empty($request->empresas)) {
            $query->whereIn('empresa_id', $request->empresas);
        }


        if ($request->status === 'PENDENTE') {
            $query->whereHas('itens', function ($q) {
                $q->where('saldo_devedor', '>', 0);
            });
        }

        if ($request->status === 'DEVOLVIDAS') {
            $query->whereDoesntHave('itens', function ($q) {
                $q->where('saldo_devedor', '>', 0);
            });
        }

        $notas = $query->orderBy('emissao')->get();

        $itens = [];

        foreach ($notas as $nota) {
            foreach ($nota->itens as $item) {
                $itens[] = [
                    'nf'       => $nota->nota_fiscal,
                    'serie'    => $nota->serie,
                    'numero'   => $nota->id,
                    'codigo'   => $item->material->codigo ?? '',
                    'produto'  => $item->material->descricao ?? '',
                    'status'   => $item->saldo_devedor > 0 ? 'PENDENTE' : 'DEVOLVIDA',
                    'faturado' => $item->faturado,
                    'saldo'    => $item->saldo_devedor,
                ];
            }
        }

        $cliente = [
            'nome_razaosocial'     => $notas->first()?->cliente->nome_razaosocial ?? '',
            'sobrenome_nomefantasia'     => $notas->first()?->cliente->sobrenome_nomefantasia ?? '',
            'cpf_cnpj'     => Formatters::cpfCnpj($notas->first()?->cliente->cpf_cnpj ?? ''),
            'telefone' => Formatters::telefone($notas->first()?->cliente->telefone ?? ''),
            'email' => $notas->first()?->cliente->email ?? '',
            'bairro' => $notas->first()?->cliente->bairro ?? '',
            'cidade' => $notas->first()?->cliente->cidade ?? '',
            'uf' => $notas->first()?->cliente->uf ?? '',
        ];

        $logotipo = $this->loadImageAsBase64(
            storage_path('app/images/platoflex.png')
        );

        $pdf = Pdf::loadView('relatorios.relatorio-por-cliente', [
            'logotipo' => $logotipo,
            'titulo'   => 'RELATÓRIO DE SUCATAS POR CLIENTE',
            'nome_fantasia' => 'Platoflex embreagens',
            'cliente'  => $cliente,
            'itens'    => $itens,
            'data'     => Carbon::now()->format('d/m/Y'),
            'hora'     => Carbon::now()->format('H:i'),
        ]);

        $pdf->setOptions(['enable_php' => true]);

        $file = $pdf->setPaper('A4')->output();

        return response(
            $file,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="relatorio-teste.pdf"',
            ]
        );
    }


    private function loadImageAsBase64(string $path): ?string
    {
        if (!file_exists($path)) {
            return null;
        }

        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);

        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}
