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

        // 🔥 SIEMPRE BORRAR TOKEN VIEJO AL HACER LOGIN
        Session::forget('jwt_token');

        // 5. Solicitar token JWT REAL a tu API de Spring
        try {
            $baseUrl = rtrim(config('services.productos.base_url', 'http://localhost:8080'), '/');

            // ⚠ Debe coincidir EXACTAMENTE con lo que recibe tu API:
            // AuthController.java → recibe documento_Empleado y contrasena sin hash
            $tokenResponse = Http::post($baseUrl . '/auth/login', [
                'documento_Empleado' => $documento,
                'contrasena'         => $clave
            ]);

            if ($tokenResponse->successful()) {
                $token = $tokenResponse->body(); // String plano
                Session::put('jwt_token', $token); // Guardar nuevo token
            } else {
                Session::forget('jwt_token'); // Si falla, borrar token
            }
        } catch (\Throwable $e) {
            // API caída o error → no usar token previo
            Session::forget('jwt_token');
        }

        // 6. Redirección según el rol
        if ($empleado->ID_Rol === 'ROL002') {
            return redirect()->route('InicioE.index'); // rol empleado
        } else {
            return redirect()->route('admin.inicio');   // rol admin
        }
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        Session::forget('jwt_token'); // 🔥 limpiar token también aquí
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('inicio');
    }
}
