<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:5|max:100',
            'sort_by' => 'sometimes|string|in:id,nome_razaosocial,sobrenome_nomefantasia,cpf_cnpj',
            'sort_dir' => 'sometimes|string|in:asc,desc',
            'filter' => 'sometimes|string|min:1|max:255',
        ]);

        $perPage = $request->get('per_page', 10);
        $sortBy = $request->get('sort_by', 'id');
        $sortDir = $request->get('sort_dir', 'asc');
        $filter = $request->get('filter');

        $query = Cliente::query();

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                $q->where('id', $filter)
                    ->orWhere('cpf_cnpj', 'like', "%{$filter}%")
                    ->orWhere('nome_razaosocial', 'like', "%{$filter}%")
                    ->orWhere('sobrenome_nomefantasia', 'like', "%{$filter}%");
            });
        }

        $clientes = $query->orderBy($sortBy, $sortDir)->paginate($perPage);

        return response()->json($clientes, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            'nome_razaosocial'       => 'required|string|max:255',
            'sobrenome_nomefantasia' => 'nullable|string|max:255',
            'cpf_cnpj'               => 'required|string|max:20|unique:clientes,cpf_cnpj',

            'cep'        => 'nullable|string|max:10',
            'logradouro' => 'nullable|string|max:255',
            'numero'     => 'nullable|string|max:20',
            'complemento'=> 'nullable|string|max:255',
            'bairro'     => 'nullable|string|max:255',
            'cidade'     => 'nullable|string|max:255',
            'uf'         => 'nullable|string|size:2',

            'telefone'   => 'nullable|string|max:20',
            'email'      => 'nullable|email|max:255',
            'observacoes'=> 'nullable|string|max:1000',
        ]);

        $cliente = Cliente::create($request->only([
            'nome_razaosocial',
            'sobrenome_nomefantasia',
            'cpf_cnpj',
            'cep',
            'logradouro',
            'numero',
            'complemento',
            'bairro',
            'cidade',
            'uf',
            'telefone',
            'email',
            'observacoes',
        ]));

        $cliente->load([
            'criadoPor',
            'atualizadoPor',
        ]);

        return response()->json($cliente, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $cliente = Cliente::findOrFail($id);

        $cliente->load([
            'criadoPor',
            'atualizadoPor',
        ]);

        return response()->json($cliente, 200);
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
        $cliente = Cliente::findOrFail($id);

        $this->validate($request, [
            'nome_razaosocial'       => 'required|string|max:255',
            'sobrenome_nomefantasia' => 'nullable|string|max:255',
            'cpf_cnpj'               => [
                'required',
                'string',
                'max:20',
                Rule::unique('clientes', 'cpf_cnpj')->ignore($cliente->id),
            ],

            'cep'        => 'nullable|string|max:10',
            'logradouro' => 'nullable|string|max:255',
            'numero'     => 'nullable|string|max:20',
            'complemento'=> 'nullable|string|max:255',
            'bairro'     => 'nullable|string|max:255',
            'cidade'     => 'nullable|string|max:255',
            'uf'         => 'nullable|string|size:2',

            'telefone'   => 'nullable|string|max:20',
            'email'      => 'nullable|email|max:255',
            'observacoes'=> 'nullable|string|max:1000',
        ]);

        $cliente->update($request->only([
            'nome_razaosocial',
            'sobrenome_nomefantasia',
            'cpf_cnpj',
            'cep',
            'logradouro',
            'numero',
            'complemento',
            'bairro',
            'cidade',
            'uf',
            'telefone',
            'email',
            'observacoes',
        ]));

        $cliente->load([
            'criadoPor',
            'atualizadoPor',
        ]);

        return response()->json($cliente, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id)
    {
        $cliente = Cliente::findOrFail($id);

        $cliente->delete();

        return response()->json([
            'message' => 'Cliente excluído com sucesso!'
        ], 200);
    }
}
