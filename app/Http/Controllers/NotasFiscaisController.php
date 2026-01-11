<?php

namespace App\Http\Controllers;

use App\Models\NotaFiscal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NotasFiscaisController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $empresas = collect(explode(',', (string)$request->query('empresas')))
            ->filter(fn($v) => is_numeric($v))
            ->map(fn($v) => (int)$v)
            ->values()
            ->all();

        $request->merge([
            'empresas' => $empresas
        ]);

        $this->validate($request, [
            'empresas' => 'required|array|min:1',
            'empresas.*' => 'required|integer|exists:empresas,id',
            'cliente' => 'required|integer|exists:clientes,id',
            'status' => 'required|in:TODAS,PENDENTE,DEVOLVIDA'
        ]);

        // 4. Query
        $q = NotaFiscal::query();

        $q->whereIn('empresa_id', $empresas);
        $q->where('cliente_id', $request->input('cliente'));

        if ($request->input('status') !== 'TODAS') {
            $q->where('status', $request->input('status'));
        }

        $q->orderBy('emissao', 'desc')
            ->orderBy('nota_fiscal')
            ->orderBy('serie');

        return response()->json($q->get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'empresa_id' => 'required|integer|exists:empresas,id',
            'cliente_id' => 'required|integer|exists:clientes,id',
            'nota_fiscal' => 'required|integer',
            'serie' => 'required|integer',
            'emissao' => 'required|date',
        ]);

        $notaFiscal = NotaFiscal::create([
            'empresa_id' => $request->input('empresa_id'),
            'cliente_id' => $request->input('cliente_id'),
            'nota_fiscal' => $request->input('nota_fiscal'),
            'serie' => $request->input('serie'),
            'emissao' => $request->input('emissao')
        ]);

        $notaFiscal->load(['empresa', 'cliente']);

        return response()->json($notaFiscal, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id)
    {
        $notaFiscal = NotaFiscal::with(['empresa', 'cliente'])->findOrFail($id);

        return response()->json($notaFiscal, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $notaFiscal = NotaFiscal::findOrFail($id);

        $this->validate($request, [
            'empresa_id' => 'required|integer|exists:empresas,id',
            'cliente_id' => 'required|integer|exists:clientes,id',
            'nota_fiscal' => [
                'required',
                'integer',
                Rule::unique('notas_fiscais')
                    ->where('empresa_id', $request->input('empresa_id'))
                    ->where('serie', $request->input('serie'))
                    ->ignore($notaFiscal->id),
            ],
            'serie' => 'required|integer',
            'emissao' => 'required|date',
        ]);

        $notaFiscal->update([
            'empresa_id' => $request->input('empresa_id'),
            'cliente_id' => $request->input('cliente_id'),
            'nota_fiscal' => $request->input('nota_fiscal'),
            'serie' => $request->input('serie'),
            'emissao' => $request->input('emissao'),
        ]);

        $notaFiscal->load(['empresa', 'cliente']);

        return response()->json($notaFiscal, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $notaFiscal = NotaFiscal::findOrFail($id);

        $notaFiscal->delete();

        return response()->json([
            'message' => 'Nota fiscal removida com sucesso.'
        ], 200);
    }
}
