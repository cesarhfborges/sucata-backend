<?php

namespace App\Http\Controllers;

use App\Models\NotaFiscal;
use App\Models\NotaFiscalItem;
use App\Support\Formatters;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RelatoriosController extends Controller
{

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

        $query = NotaFiscalItem::query()
            ->with([
                'notaFiscal.cliente',
                'notaFiscal.empresa',
                'material',
            ])
            ->whereHas('notaFiscal', function ($q) use ($request) {

                // Cliente
                $q->where('cliente_id', $request->cliente_id);

                // Datas
                $q->whereBetween('emissao', [
                    $request->datas['inicio'],
                    $request->datas['fim'],
                ]);

                // Empresas
                if (!empty($request->empresas)) {
                    $q->whereIn('empresa_id', $request->empresas);
                }
            });

        // 🔥 FILTRO DE STATUS NO NÍVEL CORRETO
        if ($request->status === 'PENDENTE') {
            $query->where('saldo_devedor', '>', 0);
        }

        if ($request->status === 'DEVOLVIDAS') {
            $query->where('saldo_devedor', '=', 0);
        }

        $itensModel = $query
            ->orderBy(
                NotaFiscal::select('emissao')
                    ->whereColumn('notas_fiscais.id', 'nota_fiscal_itens.nota_fiscal_id')
            )
            ->get();

        $clienteModel = $itensModel->first()?->notaFiscal?->cliente;

        $cliente = [
            'nome_razaosocial' => $clienteModel->nome_razaosocial ?? '',
            'sobrenome_nomefantasia' => $clienteModel->sobrenome_nomefantasia ?? '',
            'cpf_cnpj' => Formatters::cpfCnpj($clienteModel->cpf_cnpj ?? ''),
            'telefone' => Formatters::telefone($clienteModel->telefone ?? ''),
            'email' => $clienteModel->email ?? '',
            'bairro' => $clienteModel->bairro ?? '',
            'cidade' => $clienteModel->cidade ?? '',
            'uf' => $clienteModel->uf ?? '',
        ];

        $itens = $itensModel->map(function (NotaFiscalItem $item) {
            return [
                'nf'            => $item->notaFiscal->nota_fiscal,
                'serie'         => $item->notaFiscal->serie,
                'numero'        => $item->nota_fiscal_id,
                'codigo'        => $item->material->codigo ?? '',
                'produto'       => $item->material->descricao ?? '',
                'status'        => $item->saldo_devedor > 0 ? 'PENDENTE' : 'DEVOLVIDA',
                'faturado'      => $item->faturado,
                'saldo_devedor' => $item->saldo_devedor,
            ];
        });

        // PDF
        $logotipo = $this->loadImageAsBase64(
            storage_path('app/images/platoflex.png')
        );

        $pdf = Pdf::loadView('relatorios.relatorio-por-cliente', [
            'logotipo' => $logotipo,
            'titulo' => 'RELATÓRIO DE SUCATAS POR CLIENTE',
            'cliente' => $cliente,
            'itens' => $itens,
            'total' => [
                'quantidade' => $itens->count(),
                'saldo_devedor' => $itens->sum('saldo_devedor'),
                'faturado' => $itens->sum('faturado'),
            ],
            'data'     => Carbon::now()->format('d/m/Y'),
            'hora'     => Carbon::now()->format('H:i'),
        ]);

        $pdf->setOptions(['enable_php' => true]);

        return response(
            $pdf->setPaper('A4')->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="relatorio-por-cliente.pdf"',
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
