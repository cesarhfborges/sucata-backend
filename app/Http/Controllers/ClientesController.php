<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        //
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
