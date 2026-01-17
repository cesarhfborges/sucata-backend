<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PerfilController extends Controller
{
    public function perfil()
    {
        $user = Auth::user();
        $user->load([
            'criadoPor',
            'atualizadoPor',
        ]);
        return response()->json($user, 200);
    }

    public function updatePerfil(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $this->validate($request, [
            'nome' => 'string|max:255',
            'sobrenome' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $user->id,

            'password'        => 'sometimes|nullable|min:6',
            'confirmPassword' => 'required_with:password|same:password',
        ]);

        $user->nome = $request->input('nome');
        $user->sobrenome = $request->input('sobrenome');
        $user->email = $request->input('email');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        if ($user->isDirty()) {
            $user->save();
        }

        $user->load([
            'criadoPor',
            'atualizadoPor',
        ]);

        return response()->json($user);
    }
}
