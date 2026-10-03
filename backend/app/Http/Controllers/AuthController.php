<?php

namespace App\Http\Controllers;

use App\Services\TwoFactorService;
use App\Support\RegistraBitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class AuthController extends Controller
{
    public function __construct(private readonly TwoFactorService $twoFactorService) {}

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'correo' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt([
            'correo' => $credentials['correo'],
            'password' => $credentials['password'],
        ])) {
            RegistraBitacora::registrar('auth', 'login_fallido', 'Intento web: ' . $credentials['correo'], $request);

            return back()->withErrors([
                'correo' => 'Credenciales incorrectas o usuario inactivo.',
            ]);
        }

        $request->session()->regenerate();
        $user = Auth::user()->load('rol');

        if (! $user->activo) {
            Auth::logout();

            return back()->withErrors([
                'correo' => 'Credenciales incorrectas o usuario inactivo.',
            ]);
        }

        if ($this->twoFactorService->requiereDosFactor($user)) {
            $request->session()->put('2fa_verified', false);

            if (! $this->twoFactorService->tieneSecretoConfigurado($user)) {
                return redirect()->route('login.2fa.setup');
            }

            return redirect()->route('login.2fa');
        }

        $request->session()->put('2fa_verified', true);
        RegistraBitacora::registrar('auth', 'login', 'Inicio de sesión web', $request);

        return $this->redirectByRole($user);
    }

    public function showTwoFactorSetup(Request $request)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (! $this->twoFactorService->requiereDosFactor($user)) {
            return redirect()->route('login');
        }

        if ($this->twoFactorService->tieneSecretoConfigurado($user)) {
            return redirect()->route('login.2fa');
        }

        $secreto = $request->session()->get('totp_setup_secreto');

        if (! $secreto) {
            $setup = $this->twoFactorService->generarEnrolamiento($user);
            $secreto = $setup['secreto'];
            $request->session()->put('totp_setup_secreto', $secreto);
        } else {
            $setup = $this->twoFactorService->empaquetarSecreto($user, $secreto);
        }

        return view('auth.two-factor-setup', [
            'qrSvg' => $setup['qr_svg'],
            'secreto' => $secreto,
        ]);
    }

    public function confirmTwoFactorSetup(Request $request)
    {
        $request->validate(['codigo' => 'required|digits:6']);

        $user = Auth::user();
        $secreto = $request->session()->get('totp_setup_secreto');

        if (! $secreto) {
            return redirect()->route('login.2fa.setup');
        }

        try {
            $this->twoFactorService->confirmarEnrolamiento($user, $secreto, $request->input('codigo'));
        } catch (RuntimeException $e) {
            return back()->withErrors(['codigo' => $e->getMessage()]);
        }

        $request->session()->forget('totp_setup_secreto');
        $request->session()->put('2fa_verified', true);
        RegistraBitacora::registrar('auth', '2fa_configurado', 'Google Authenticator vinculado', $request);

        return $this->redirectByRole($user->load('rol'))
            ->with('success', 'Google Authenticator configurado correctamente.');
    }

    public function showTwoFactor()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($this->twoFactorService->requiereDosFactor($user) && ! $this->twoFactorService->tieneSecretoConfigurado($user)) {
            return redirect()->route('login.2fa.setup');
        }

        return view('auth.two-factor');
    }

    public function verifyTwoFactor(Request $request)
    {
        $request->validate(['codigo' => 'required|digits:6']);

        $user = Auth::user();

        try {
            $this->twoFactorService->verificarCodigo($user, $request->input('codigo'));
        } catch (RuntimeException $e) {
            RegistraBitacora::registrar('auth', '2fa_fallido', '2FA web fallido', $request);

            return back()->withErrors(['codigo' => $e->getMessage()]);
        }

        $request->session()->put('2fa_verified', true);
        RegistraBitacora::registrar('auth', '2fa', 'Segundo factor validado (Google Authenticator)', $request);

        return $this->redirectByRole($user->load('rol'));
    }

    public function logout(Request $request)
    {
        RegistraBitacora::registrar('auth', 'logout', 'Cierre de sesión', $request);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    private function redirectByRole($user)
    {
        if ($user->isAdmin() || $user->isSoporte()) {
            return redirect()->route('dashboard');
        }

        return redirect()->route('solicitudes.index');
    }
}
