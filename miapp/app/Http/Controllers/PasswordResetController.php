<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController
{
    // Muestra formulario "Olvidé mi contraseña"
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // Envía el email con el enlace
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // ✅ Criterio 5: No revelar si el email existe o no
        $empleado = DB::table('Empleados')
            ->where('Correo_Electronico', $request->email)
            ->first();

        // Siempre mostramos el mismo mensaje
        if (!$empleado) {
            return back()->with('mensaje', 'Si ese correo está registrado, recibirás un enlace en breve.');
        }

        // Eliminar tokens anteriores del mismo empleado
        DB::table('password_resets')
            ->where('documento_empleado', $empleado->Documento_Empleado)
            ->delete();

        // Generar token único
        $token = Str::random(64);

        // Guardar token
        DB::table('password_resets')->insert([
            'documento_empleado' => $empleado->Documento_Empleado,
            'token'              => $token,
            'created_at'         => now(),
            'used'               => 0
        ]);

        // Enviar email
        $resetUrl = route('password.reset', ['token' => $token]);
        $nombre   = $empleado->Nombre_Usuario;

        Mail::send('emails.reset-password', 
            ['resetUrl' => $resetUrl, 'nombre' => $nombre], 
            function($message) use ($request) {
                $message->to($request->email)
                        ->subject('Restablecer contraseña - TECNICELL RM');
            }
        );

        return back()->with('mensaje', 'Si ese correo está registrado, recibirás un enlace en breve.');
    }

    // Muestra formulario nueva contraseña
    public function showResetForm($token)
    {
        // Verificar que el token existe, no está usado y no expiró (60 minutos)
        $reset = DB::table('password_resets')
            ->where('token', $token)
            ->where('used', 0)
            ->where('created_at', '>=', now()->subMinutes(60))
            ->first();

        if (!$reset) {
            return redirect()->route('password.forgot')
                ->with('error', 'El enlace es inválido o ha expirado. Solicita uno nuevo.');
        }

        return view('auth.reset-password', compact('token'));
    }

    // Actualiza la contraseña
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'                 => 'required|string',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string'
        ], [
            'password.min'          => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'    => 'Las contraseñas no coinciden.',
        ]);

        // Verificar token
        $reset = DB::table('password_resets')
            ->where('token', $request->token)
            ->where('used', 0)
            ->where('created_at', '>=', now()->subMinutes(60))
            ->first();

        if (!$reset) {
            return redirect()->route('password.forgot')
                ->with('error', 'El enlace es inválido o ha expirado. Solicita uno nuevo.');
        }

        // Actualizar contraseña con SHA256
        $nuevoHash = hash('sha256', $request->password);

        DB::table('Contrasenas')
            ->where('Documento_Empleado', $reset->documento_empleado)
            ->update(['Contrasena_Hash' => $nuevoHash]);

        // ✅ Criterio 4: Invalidar el token después de usado
        DB::table('password_resets')
            ->where('token', $request->token)
            ->update(['used' => 1]);

        return redirect()->route('login.form')
            ->with('mensaje', '¡Contraseña actualizada correctamente! Ya puedes iniciar sesión.');
    }
}