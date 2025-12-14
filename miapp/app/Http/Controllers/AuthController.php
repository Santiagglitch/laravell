<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;

class AuthController
{
    // Mostrar el formulario de login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Procesar el login (POST)
    public function login(Request $request)
    {
        // 1. Validar campos
        $request->validate([
            'usuario'    => 'required|string',
            'contrasena' => 'required|string',
        ], [
            'usuario.required'    => 'El documento es obligatorio.',
            'contrasena.required' => 'La contraseña es obligatoria.',
        ]);

        $documento = trim($request->input('usuario'));
        $clave     = $request->input('contrasena');

        // 2. Hash igual a tu trigger (SHA2 256)
        $hashClave = hash('sha256', $clave);

        // 3. Validar contra tu BD local (MySQL FONRIO)
        $empleado = DB::table('Empleados as E')
            ->join('Contrasenas as C', 'C.Documento_Empleado', '=', 'E.Documento_Empleado')
            ->where('E.Documento_Empleado', $documento)
            ->where('C.Contrasena_Hash', $hashClave)
            ->select('E.Documento_Empleado', 'E.Nombre_Usuario', 'E.ID_Rol')
            ->first();

        if (! $empleado) {
            return back()
                ->withErrors(['login' => 'Usuario o contraseña incorrecta.'])
                ->withInput();
        }

        // 4. Regenerar sesión y guardar datos
        $request->session()->regenerate();
        Session::put('documento', $empleado->Documento_Empleado);
        Session::put('nombre',    $empleado->Nombre_Usuario);
        Session::put('rol',       $empleado->ID_Rol);

        // 🔥 borrar token viejo
        Session::forget('jwt_token');
        Session::forget('rol_api');

        // 5. Solicitar token JWT REAL a la API Spring
        try {
            $baseUrl = rtrim(config('services.productos.base_url', 'http://localhost:8080'), '/');

            $tokenResponse = Http::post($baseUrl . '/auth/login', [
                'documento_Empleado' => $documento,
                'contrasena'         => $clave
            ]);

            if ($tokenResponse->successful()) {
                $json  = $tokenResponse->json();
                $token = $json['token'] ?? null;

                if ($token) {
                    Session::put('jwt_token', $token);
                    Session::put('rol_api', $json['rol'] ?? null);
                } else {
                    Session::forget('jwt_token');
                }
            } else {
                Session::forget('jwt_token');
            }
        } catch (\Throwable $e) {
            Session::forget('jwt_token');
        }

        // 6. Redirección según el rol
        if ($empleado->ID_Rol === 'ROL002') {
            return redirect()->route('InicioE.index');
        }

        return redirect()->route('admin.inicio');
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        Session::forget('jwt_token');
        Session::forget('rol_api');

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('inicio');
    }
}
