<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\NotaFiscalItem;
use App\Support\Formatters;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Throwable;

class RelatoriosController extends Controller
{

    /**
     * @throws MpdfException
     * @throws Throwable
     */
    public function porCliente(Request $request)
    {
        // =========================
        // VALIDAÇÃO
        // =========================
        $this->validate($request, [
            'empresas' => 'required|array|min:1',
            'empresas.*' => 'integer|exists:empresas,id',

            'status' => 'required|in:TODAS,PENDENTE,DEVOLVIDAS',

            'cliente_id' => 'nullable|integer|exists:clientes,id',

            'datas.inicio' => 'nullable|date',
            'datas.fim' => 'nullable|date|after_or_equal:datas.inicio',
        ]);

        // =========================
        // QUERY PRINCIPAL (ITENS)
        // =========================
        $query = NotaFiscalItem::query()
            ->select([
                'nota_fiscal_itens.id',
                'nota_fiscal_itens.nota_fiscal_id',
                'nota_fiscal_itens.material_id',
                'nota_fiscal_itens.faturado',
                'nota_fiscal_itens.saldo_devedor',
            ])
            ->with([
                'material:codigo,descricao',
                'notaFiscal:id,empresa_id,cliente_id,nota_fiscal,serie,emissao',
//                'notaFiscal.cliente:id,nome_razaosocial,sobrenome_nomefantasia,cpf_cnpj,telefone,email',
            ])
            ->whereHas('notaFiscal', function ($q) use ($request) {

                // Empresas (obrigatório)
                $q->whereIn('empresa_id', $request->empresas);

                // Cliente (opcional)
                if (!empty($request->cliente_id)) {
                    $q->where('cliente_id', $request->cliente_id);
                }

                // Datas (opcional)
                if (!empty($request->datas['inicio']) && !empty($request->datas['fim'])) {
                    $q->whereBetween('emissao', [
                        $request->datas['inicio'],
                        $request->datas['fim'],
                    ]);
                }
            });

        // Status
        if ($request->status === 'PENDENTE') {
            $query->where('saldo_devedor', '>', 0);
        } elseif ($request->status === 'DEVOLVIDAS') {
            $query->where('saldo_devedor', '=', 0);
        }

        $itens = $query
            ->orderBy('nota_fiscal_id')
            ->get();

        $cliente = null;

        if ($request->cliente_id) {
            $c = Cliente::findOrFail($request->cliente_id);
            $cliente = [
                'nome_razaosocial' => $c->nome_razaosocial,
                'sobrenome_nomefantasia' => $c->sobrenome_nomefantasia,
                'cpf_cnpj' => Formatters::cpfCnpj($c->cpf_cnpj),
                'telefone' => Formatters::telefone($c->telefone),
                'email' => $c->email,
            ];
        }

        // =========================
        // TOTAIS (SEM LOOP MANUAL)
        // =========================
        $totais = (object)[
            'quantidade' => $itens->count(),
            'faturado' => $itens->sum('faturado'),
            'saldo_devedor' => $itens->sum('saldo_devedor'),
        ];

        // =========================
        // mPDF
        // =========================
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 5,
            'margin_bottom' => 10,
            'tempDir' => storage_path('app/mpdf'),
        ]);

        $mpdf->WriteHTML(
            view('relatorios.header', [
                'logotipo' => $this->loadImageAsBase64(storage_path('app/images/platoflex.png')),
                'data' => Carbon::now()->format('d/m/Y'),
                'hora' => Carbon::now()->format('H:i'),
                'cliente' => $cliente,
            ])->render()
        );

        if ($request->cliente_id) {
            $itens->chunk(200)->each(function ($chunk) use ($totais, $mpdf) {
                $mpdf->WriteHTML(
                    view('relatorios.table', ['itens' => $chunk, 'totais' => $totais])->render()
                );
            });
        } else {
            $itens->chunk(200)->each(function ($chunk) use ($totais, $mpdf) {
                $mpdf->WriteHTML(
                    view('relatorios.cliente-table', ['itens' => $chunk, 'totais' => $totais])->render()
                );
            });
        }

        $mpdf->WriteHTML(
            view('relatorios.footer', compact('cliente'))->render()
        );

        return response(
            $mpdf->Output('relatorio.pdf', 'S'),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="relatorio.pdf"',
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
