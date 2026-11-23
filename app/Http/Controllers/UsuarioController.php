<?php

namespace App\Http\Controllers;

use App\Models\Sesion;
use App\Models\SolicitudClave;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index()
    {
        return view('usuarios.index', [
            'usuarios' => Usuario::orderBy('codigo')->get(),
            'proximoCodigo' => Usuario::proximoCodigo(),
            'solicitudes' => SolicitudClave::with('usuario')->where('estado', 'pendiente')->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);
        $datos['codigo'] = Usuario::proximoCodigo();
        $datos['estado'] = 'activo';

        Usuario::create($datos);

        return back()->with('exito', "Usuario {$datos['usuario']} creado.");
    }

    public function edit(Usuario $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $datos = $this->validar($request, $usuario);
        if (empty($datos['password'])) {
            unset($datos['password']);
        }

        $usuario->update($datos);

        return redirect()->route('usuarios.index')->with('exito', 'Usuario actualizado.');
    }

    public function desactivar(Usuario $usuario)
    {
        if ($error = $this->protegerUltimoAdmin($usuario)) {
            return back()->with('error', $error);
        }

        $usuario->update(['estado' => 'inactivo']);

        return back()->with('exito', "Usuario {$usuario->usuario} desactivado.");
    }

    public function reactivar(Usuario $usuario)
    {
        $usuario->update(['estado' => 'activo']);

        return back()->with('exito', "Usuario {$usuario->usuario} reactivado.");
    }

    // Solo se elimina si no tiene ventas ni sesiones registradas
    public function destroy(Usuario $usuario)
    {
        if ($error = $this->protegerUltimoAdmin($usuario)) {
            return back()->with('error', $error);
        }
        if ($usuario->ventas()->exists() || $usuario->sesiones()->exists()) {
            return back()->with('error', 'El usuario tiene ventas o sesiones registradas. Solo puede desactivarse.');
        }

        $usuario->delete();

        return back()->with('exito', 'Usuario eliminado.');
    }

    // Genera una clave temporal y la muestra una sola vez al administrador
    public function restablecer(Usuario $usuario)
    {
        $temporal = 'Tf' . Str::random(6) . '!';
        $usuario->update(['password' => $temporal]);

        SolicitudClave::where('usuario_id', $usuario->id)->where('estado', 'pendiente')
            ->update(['estado' => 'atendida', 'atendida_por' => auth()->id()]);

        return back()->with('exito', "Clave restablecida para {$usuario->usuario}. Nueva clave temporal: {$temporal} — entrégala al usuario y pídele que la cambie.");
    }

    public function sesiones()
    {
        return view('usuarios.sesiones', [
            'sesiones' => Sesion::with('usuario')->latest('inicio')->paginate(20),
        ]);
    }

    private function protegerUltimoAdmin(Usuario $usuario): ?string
    {
        $adminsActivos = Usuario::where('rol', 'administrador')->where('estado', 'activo')->count();

        if ($usuario->rol === 'administrador' && $usuario->estado === 'activo' && $adminsActivos <= 1) {
            return 'No se puede desactivar ni eliminar al último administrador activo.';
        }

        return null;
    }

    private function validar(Request $request, ?Usuario $usuario = null): array
    {
        $reglasClave = $usuario ? ['nullable', 'string', 'min:6', 'confirmed'] : ['required', 'string', 'min:6', 'confirmed'];

        return $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'ci' => ['required', 'string', 'max:20', Rule::unique('usuarios', 'ci')->ignore($usuario)],
            'usuario' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('usuarios', 'usuario')->ignore($usuario)],
            'password' => $reglasClave,
            'rol' => ['required', Rule::in(['administrador', 'vendedor'])],
        ], [
            'usuario.unique' => 'Ese nombre de usuario ya está en uso.',
            'ci.unique' => 'Ya existe un usuario con ese CI.',
            'password.confirmed' => 'La confirmación de la clave no coincide.',
            'password.min' => 'La clave debe tener al menos 6 caracteres.',
        ]);
    }
}
