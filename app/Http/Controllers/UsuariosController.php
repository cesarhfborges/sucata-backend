<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsuariosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $e = User::all();
        return response()->json($e, 200);
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
            'nome' => 'string|max:255',
            'sobrenome' => 'string|max:255',
            'email' => 'email|unique:users,email',

            'password'        => 'required|min:6',
            'confirmPassword' => 'required|same:password',

            'ativo'           => 'sometimes|boolean'
        ]);

        $user = new User();

        $user->nome = $request->input('nome');
        $user->sobrenome = $request->input('sobrenome');
        $user->email = $request->input('email');


        $user->password = Hash::make($request->input('password'));

        if ($request->filled('ativo')) {
            $user->ativo = $request->input('ativo');
        }

        $user->save();

        $user->load([
            'criadoPor',
            'atualizadoPor',
        ]);

        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->load([
            'criadoPor',
            'atualizadoPor',
        ]);
        return response()->json($user, 200);
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
        $user = User::findOrFail($id);

        $this->validate($request, [
            'nome' => 'string|max:255',
            'sobrenome' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $user->id,

            'password'        => 'sometimes|nullable|min:6',
            'confirmPassword' => 'required_with:password|same:password',
        ]);

        $user->fill($request->only(['nome', 'sobrenome', 'email']));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        $user->load([
            'criadoPor',
            'atualizadoPor',
        ]);

        return response()->json($user, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        if (Auth::id() === $id) {
            return response()->json([
                'message' => 'Não é permitido excluir seu próprio usuário.'
            ], 405);
        }

        $empresa = User::findOrFail($id);

        $empresa->delete();

        return response()->json([
            'message' => 'Usuário excluído com sucesso!'
        ], 200);
    }

    public function checkEmailAvailability(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'id'    => 'sometimes|integer|nullable' // ID é opcional
        ]);

        $email = $request->input('email');
        $id = $request->input('id');

        // Busca se existe algum usuário com esse email,
        // MAS que não seja o usuário do ID informado
        $exists = User::where('email', $email)
            ->when($id, function ($query, $id) {
                return $query->where('id', '!=', $id);
            })
            ->exists();

        return response()->json([
            'available' => !$exists
        ]);
    }
}
