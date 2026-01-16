<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string',
            'remember' => 'sometimes|boolean'
        ]);

        $credentials = $request->only(['email', 'password']);
        $remember = $request->input('remember', false);

        if ($remember) {
            Auth::factory()->setTTL(480);
        }

        if (!$token = Auth::attempt($credentials)) {
            Log::warning('Falha de login para o usuário informado', [
                'type' => 'auth.failed',
                'email' => $request->input('email'),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            throw new UnauthorizedHttpException('Bearer', __('auth.failed'));
        }

        /** @var User $u */
        $u = Auth::user();
        $u->ult_acesso = new DateTime();
        $u->update();

        $ttlEmMinutos = Auth::factory()->getTTL();
        $expiresAt = Carbon::now()->addMinutes($ttlEmMinutos);

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_at' => $expiresAt->format('Y-m-d\TH:i:s')
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        return response()->json([
            'message' => 'Logout realizado com sucesso'
        ]);
    }
}
