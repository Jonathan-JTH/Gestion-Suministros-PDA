<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->segundo_factor_habilitado && ! $request->session()->get('2fa_verified')) {
            return redirect()->route('login.2fa');
        }

        return $next($request);
    }
}
