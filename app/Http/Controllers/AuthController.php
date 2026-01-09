<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            throw new UnauthorizedHttpException('Bearer', __('auth.failed'));
        }

        /** @var User $u */
        $u = Auth::user();
        $u->ult_acesso = new DateTime();
        $u->update();

        $ttlEmMinutos = Auth::factory()->getTTL();
        $expiresAt = Carbon::now()->addMinutes($ttlEmMinutos);

        $ATUAL = Carbon::now();

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_at' => $expiresAt->format('Y-m-d\TH:i:s')
        ]);
    }
}
