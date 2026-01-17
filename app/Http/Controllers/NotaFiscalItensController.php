<?php

namespace App\Http\Controllers;

use App\Models\NotaFiscalItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class NotaFiscalItensController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function index(int $id)
    {
        $q = NotaFiscalItem::query();
        $q->with(['material']);
        $q->where('nota_fiscal_id', $id);
        $lista = $q->get();
        return response()->json($lista, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function store(Request $request, int $id): JsonResponse
    {
        $this->validate($request, [
            'material_id' => [
                'required',
                'string',
                Rule::unique('nota_fiscal_itens')
                    ->where('nota_fiscal_id', $id),
            ],
            'faturado' => 'required|integer|min:1',
            'saldo_devedor' => [
                'required',
                'integer',
                'min:0',
                'lte:faturado',
            ],
        ],
            [
                'faturado.min' => 'O faturado deve ser no mínimo 1.',
                'saldo_devedor.min' => 'O saldo devedor deve ser maior que zero.',
                'saldo_devedor.lte' => 'O saldo devedor não pode ser maior que o faturado.',
            ]
        );

        $item = NotaFiscalItem::create([
            'nota_fiscal_id' => $id,
            'material_id' => $request->input('material_id'),
            'faturado' => $request->input('faturado'),
            'saldo_devedor' => $request->input('saldo_devedor'),
        ]);

        $item->load([
            'material',
            'criadoPor',
            'atualizadoPor',
        ]);

        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @param int $itemId
     * @return JsonResponse
     */
    public function show(int $id, int $itemId): JsonResponse
    {
        $item = NotaFiscalItem::with(['material'])
            ->where('nota_fiscal_id', $id)
            ->findOrFail($itemId);

        $item->load([
            'material',
            'criadoPor',
            'atualizadoPor',
        ]);

        return response()->json($item, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @param int $itemId
     * @return JsonResponse
     */
    public function update(Request $request, int $id, int $itemId): JsonResponse
    {
        $item = NotaFiscalItem::where('nota_fiscal_id', $id)
            ->findOrFail($itemId);

        $this->validate($request, [
            'material_id' => [
                'required',
                'string',
                Rule::unique('nota_fiscal_itens')
                    ->where('nota_fiscal_id', $id)
                    ->ignore($item->id),
            ],
            'faturado' => 'required|integer|min:1',
            'saldo_devedor' => [
                'required',
                'integer',
                'min:0',
                'lte:faturado',
            ],
        ],
            [
                'faturado.min' => 'O faturado deve ser no mínimo 1.',
                'saldo_devedor.min' => 'O saldo devedor deve ser maior que zero.',
                'saldo_devedor.lte' => 'O saldo devedor não pode ser maior que o faturado.',
            ]
        );

        $item->update([
            'material_id' => $request->input('material_id'),
            'faturado' => $request->input('faturado'),
            'saldo_devedor' => $request->input('saldo_devedor'),
        ]);

        $item->load([
            'material',
            'criadoPor',
            'atualizadoPor',
        ]);

        return response()->json($item, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @param int $itemId
     * @return JsonResponse
     */
    public function destroy(int $id, int $itemId): JsonResponse
    {
        $item = NotaFiscalItem::where('nota_fiscal_id', $id)
            ->findOrFail($itemId);

        $item->delete();

        return response()->json([
            'message' => 'Item da nota fiscal removido com sucesso.'
        ], 200);
    }

    /**
     * Realiza movimentacao
     *
     * @param Request $request
     * @param int $id
     * @param int $itemId
     * @return JsonResponse
     */
    public function movimentar(Request $request, int $id, int $itemId): JsonResponse
    {
        $item = NotaFiscalItem::where('nota_fiscal_id', $id)
            ->findOrFail($itemId);

        // Validação básica do payload
        $this->validate($request, [
            'acao' => ['required', Rule::in(['baixar', 'extornar'])],
            'quantidade' => ['required', 'integer', 'min:1'],
        ], [
            'acao.in' => 'A ação deve ser baixar ou extornar.',
            'quantidade.min' => 'A quantidade deve ser no mínimo 1.',
        ]);

        $acao = $request->input('acao');
        $quantidade = (int) $request->input('quantidade');

        /*
         * ======================
         * BAIXAR DÉBITO
         * ======================
         */
        if ($acao === 'baixar') {
            if ($item->saldo_devedor <= 0) {
                throw ValidationException::withMessages([
                    'saldo_devedor' => 'Este item não possui saldo devedor para baixar.'
                ]);
            }

            if ($quantidade > $item->saldo_devedor) {
                throw ValidationException::withMessages([
                    'quantidade' => 'A quantidade para baixa não pode ser maior que o saldo devedor.'
                ]);
            }

            $item->saldo_devedor -= $quantidade;
        }

        /*
         * ======================
         * EXTORNAR
         * ======================
         */
        if ($acao === 'extornar') {
            $maxExtornavel = $item->faturado - $item->saldo_devedor;

            if ($maxExtornavel <= 0) {
                throw ValidationException::withMessages([
                    'saldo_devedor' => 'Não há quantidade disponível para estorno.'
                ]);
            }

            if ($quantidade > $maxExtornavel) {
                throw ValidationException::withMessages([
                    'quantidade' => "A quantidade para estorno não pode ser maior que {$maxExtornavel}."
                ]);
            }

            $item->saldo_devedor += $quantidade;
        }

        $item->save();
        $item->load([
            'material',
            'criadoPor',
            'atualizadoPor',
        ]);

        return response()->json($item, 200);
    }

}

