<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmpresasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $e = Empresa::all();
        return response()->json($e, 200);
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
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'required|string|max:255',
            'cnpj' => 'required|string|size:14|unique:empresas,cnpj',
            'cep' => 'nullable|string|max:8',
            'logradouro' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:10',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'uf' => 'nullable|string|size:2',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'observacoes' => 'nullable|string',
        ]);

        $empresa = Empresa::create($request->all());

        // 3. Retorno com Status 201 (Created)
        return response()->json([
            'message' => 'Empresa cadastrada com sucesso!',
            'data' => $empresa
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id)
    {
        $empresa = Empresa::findOrFail($id);
        return response()->json(['data' => $empresa], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $empresa = Empresa::findOrFail($id);

        // 2. Validação
        $this->validate($request, [
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'required|string|max:255',
            // O terceiro parâmetro do unique ({$id}) permite salvar se o CNPJ for o mesmo desta empresa
            'cnpj' => "required|string|size:14|unique:empresas,cnpj,{$id}",
            'cep' => 'nullable|string|max:8',
            'logradouro' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:10',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'uf' => 'nullable|string|size:2',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'observacoes' => 'nullable|string',
        ]);

        $empresa->fill($request->all());
        $empresa->save();

        return response()->json([
            'message' => 'Empresa atualizada com sucesso!',
            'data' => $empresa
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $empresa = Empresa::findOrFail($id);

        try {
            // 2. Tenta excluir o registro
            $empresa->delete();

            return response()->json([
                'message' => 'Empresa excluída com sucesso!'
            ], 200);

        } catch (QueryException $e) {
            // 3. Trata erro de integridade (ex: empresa tem notas fiscais)
            if ($e->getCode() === "23000") { // Código SQL para violação de constraint
                return response()->json([
                    'error' => 'Conflict',
                    'message' => 'Não é possível excluir esta empresa pois existem registros vinculados a ela.'
                ], 409); // 409 Conflict é o ideal para violações de regra de negócio
            }

            return response()->json([
                'error' => 'Internal Server Error',
                'message' => 'Ocorreu um erro ao tentar excluir a empresa.'
            ], 500);
        }
    }
}
