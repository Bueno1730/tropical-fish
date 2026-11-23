<?php

namespace App\Http\Controllers;

use App\Models\Sesion;
use App\Models\SolicitudClave;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('inicio');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credenciales = $request->validate([
            'usuario' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'usuario.required' => 'Ingresa tu nombre de usuario.',
            'password.required' => 'Ingresa tu contraseña.',
        ]);

        // Bloqueo temporal tras varios intentos fallidos (CDU-01)
        if ($request->session()->get('login_bloqueado_hasta') && now()->lt($request->session()->get('login_bloqueado_hasta'))) {
            $segundos = now()->diffInSeconds($request->session()->get('login_bloqueado_hasta'));
            return back()->withErrors(['usuario' => "Demasiados intentos fallidos. Espera {$segundos} segundos e inténtalo de nuevo."])
                ->onlyInput('usuario');
        }

        if (! Auth::attempt(['usuario' => $credenciales['usuario'], 'password' => $credenciales['password']], $request->boolean('recordarme'))) {
            $intentos = $request->session()->increment('login_intentos');
            if ($intentos >= 5) {
                $request->session()->put('login_bloqueado_hasta', now()->addMinute());
                $request->session()->put('login_intentos', 0);
            }

            return back()->withErrors(['usuario' => 'Usuario o contraseña incorrectos.'])
                ->onlyInput('usuario');
        }

        $usuario = Auth::user();

        if (! $usuario->estaActivo()) {
            Auth::logout();
            return back()->withErrors(['usuario' => 'Tu cuenta está desactivada. Contacta al administrador.'])
                ->onlyInput('usuario');
        }

        $request->session()->forget(['login_intentos', 'login_bloqueado_hasta']);
        $request->session()->regenerate();

        $usuario->update(['ultimo_acceso' => now()]);

        Sesion::create([
            'usuario_id' => $usuario->id,
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
            'inicio' => now(),
            'ultima_actividad' => now(),
        ]);

        return redirect()->intended(route('inicio'));
    }

    public function logout(Request $request)
    {
        Sesion::where('usuario_id', Auth::id())->whereNull('fin')->latest('inicio')->first()?->update(['fin' => now()]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showRecuperar()
    {
        return view('auth.recuperar');
    }

    public function recuperar(Request $request)
    {
        $request->validate(['usuario' => ['required', 'string']], [
            'usuario.required' => 'Ingresa tu nombre de usuario.',
        ]);

        $usuario = Usuario::where('usuario', $request->usuario)->first();

        if ($usuario) {
            SolicitudClave::firstOrCreate(
                ['usuario_id' => $usuario->id, 'estado' => 'pendiente']
            );
        }

        // Mensaje genérico: no se revela si el usuario existe o no
        return redirect()->route('login')->with('exito', 'Solicitud enviada. El administrador restablecerá tu clave de acceso.');
    }
}
