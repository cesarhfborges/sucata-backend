<?php

namespace App\Http\Controllers;

use App\Models\NotaFiscal;
use App\Models\NotaFiscalItem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class EstatisticasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function resumo(): JsonResponse
    {
        // Itens pendentes
        $itensPendentes = NotaFiscalItem::where('saldo_devedor', '>', 0)->count();

        // Notas pendentes (nota que tenha pelo menos 1 item pendente)
        $notasPendentes = NotaFiscal::whereHas('itens', function ($q) {
            $q->where('saldo_devedor', '>', 0);
        })->count();

        // Saldo devedor total
        $saldoDevedorTotal = NotaFiscalItem::where('saldo_devedor', '>', 0)
            ->sum('saldo_devedor');

        // Faturado no mês atual
        $inicioMes = Carbon::now()->startOfMonth();
        $fimMes = Carbon::now()->endOfMonth();

        $faturadoMes = NotaFiscalItem::whereBetween('created_at', [$inicioMes, $fimMes])
            ->sum('faturado');

        return response()->json([
            'notas_pendentes' => $notasPendentes,
            'itens_pendentes' => $itensPendentes,
            'saldo_devedor_total' => $saldoDevedorTotal,
            'faturado_mes' => $faturadoMes,
        ], 200);
    }

    public function statusGeral(): JsonResponse
    {
        $totalNotas = NotaFiscal::count();

        $notasPendentes = NotaFiscal::whereHas('itens', function ($q) {
            $q->where('saldo_devedor', '>', 0);
        })->count();

        $notasQuitadas = $totalNotas - $notasPendentes;

        return response()->json([
            'total_notas' => $totalNotas,
            'pendentes' => $notasPendentes,
            'quitadas' => $notasQuitadas,
            'percentual_pendente' => $totalNotas > 0
                ? round(($notasPendentes / $totalNotas) * 100, 2)
                : 0,
        ]);
    }

    public function materiaisComMaiorDebito(): JsonResponse
    {
        $materiais = NotaFiscalItem::select(
            'material_id',
            DB::raw('SUM(faturado - saldo_devedor) as debito_total')
        )
            ->groupBy('material_id')
            ->orderByDesc('debito_total')
            ->limit(10)
            ->get();

        return response()->json($materiais);
    }

    public function clientesMaiorPendencia(): JsonResponse
    {
        $dados = DB::table('notas_fiscais as n')
            ->join('clientes as c', 'c.id', '=', 'n.cliente_id')
            ->join('nota_fiscal_itens as i', 'i.nota_fiscal_id', '=', 'n.id')
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('nota_fiscal_itens as i2')
                    ->whereColumn('i2.nota_fiscal_id', 'n.id')
                    ->where('i2.saldo_devedor', '>', 0);
            })
            ->where('i.saldo_devedor', '>', 0)
            ->groupBy('c.id', 'c.nome_razaosocial')
            ->selectRaw('
            c.nome_razaosocial as label,
            SUM(i.saldo_devedor) as value
        ')
            ->orderBy('value')
            ->limit(10)
            ->get();

        return response()->json([
            'labels' => $dados->pluck('label'),
            'values' => $dados->pluck('value'),
        ]);
    }

    public function ultimasMovimentacoes(): JsonResponse
    {
        $movimentacoes = DB::table('nota_fiscal_itens')
            ->select(
                'nota_fiscal_itens.id',
                'nota_fiscal_itens.material_id',
                'nota_fiscal_itens.faturado',
                'nota_fiscal_itens.saldo_devedor',
                'nota_fiscal_itens.updated_at'
            )
            ->orderByDesc('nota_fiscal_itens.updated_at')
            ->limit(15)
            ->get();

        return response()->json($movimentacoes);
    }

    public function notasPendentesPorFaixaAtraso(): JsonResponse
    {
        $hoje = Carbon::today();

        // Base: apenas notas que possuem itens pendentes
        $baseQuery = NotaFiscal::whereHas('itens', function ($q) {
            $q->where('saldo_devedor', '>', 0);
        });

        $faixas = [
            '30-60 dias' => [30, 60],
            '61-90 dias' => [61, 90],
            '91-120 dias' => [91, 120],
            '+120 dias' => [121, null],
        ];

        $resultado = [];

        foreach ($faixas as $label => [$inicio, $fim]) {
            $query = clone $baseQuery;

            $query->whereBetween(
                DB::raw("DATEDIFF('{$hoje}', emissao)"),
                [$inicio, $fim ?? 100000]
            );

            $resultado[] = [
                'label' => $label,
                'value' => $query->count(),
            ];
        }

        return response()->json([
            'labels' => collect($resultado)->pluck('label'),
            'values' => collect($resultado)->pluck('value'),
        ]);
    }
}
