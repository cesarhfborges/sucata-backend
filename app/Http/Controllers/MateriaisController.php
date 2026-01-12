<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class MateriaisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $this->validate($request, [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:5|max:100',
            'sort_by' => 'sometimes|string|in:codigo,descricao,un',
            'sort_dir' => 'sometimes|string|in:asc,desc',
            'filter' => 'sometimes|string|min:1|max:255',
        ]);

        $perPage = (int) $request->get('per_page', 10);
        $sortBy  = $request->get('sort_by', 'descricao');
        $sortDir = $request->get('sort_dir', 'asc');
        $filter  = $request->get('filter');

        $query = Material::query();

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                $q->where('codigo', 'like', "%{$filter}%")
                    ->orWhere('descricao', 'like', "%{$filter}%")
                    ->orWhere('un', 'like', "%{$filter}%");
            });
        }

        $materiais = $query
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage);

        return response()->json($materiais, 200);
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
            'codigo' => [
                'required',
                'string',
                'max:100',
                'unique:materiais,codigo',
            ],
            'descricao' => 'required|string|max:255',
            'un'        => 'required|string|max:3',
        ]);

        $material = Material::create([
            'codigo'    => $request->input('codigo'),
            'descricao' => $request->input('descricao'),
            'un'        => $request->input('un'),
        ]);

        return response()->json($material, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $material = Material::findOrFail($id);

        return response()->json($material, 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $material = Material::findOrFail($id);

        $this->validate($request, [
            'codigo' => [
                'required',
                'string',
                'max:100',
                Rule::unique('materiais', 'codigo')->ignore($material->codigo, 'codigo'),
            ],
            'descricao' => 'required|string|max:255',
            'un'        => 'required|string|max:3',
        ]);

        $material->update([
            'codigo'    => $request->input('codigo'),
            'descricao' => $request->input('descricao'),
            'un'        => $request->input('un'),
        ]);

        return response()->json($material, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $material = Material::findOrFail($id);

        $material->delete();

        return response()->json([
            'message' => 'Material removido com sucesso.'
        ], 200);
    }
}
