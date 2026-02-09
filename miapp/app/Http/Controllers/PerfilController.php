<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Contrasena;
use Illuminate\Http\Request;

class PerfilController
{
    public function mostrar()
    {
        $documento = session('documento');

        if (!$documento) {
            return redirect('/login')->with('error', 'No hay empleado en sesión');
        }

        $empleado = Empleado::where('Documento_Empleado', $documento)->first();
        $contrasena = Contrasena::where('Documento_Empleado', $documento)->first();

        
        $fotoUrl = null;
        if ($empleado && $empleado->Fotos) {
            $springBase = rtrim(config('services.spring.base_url', 'http://192.168.128.3:8080'), '/');
            $foto = trim($empleado->Fotos);

            $fotoUrl = str_starts_with($foto, 'http')
                ? $foto
                : (str_starts_with($foto, 'uploads/') ? $springBase.'/'.$foto : asset($foto));
        }

        return view('Perfil.Perfil', compact('empleado', 'contrasena', 'fotoUrl'));
    }

    public function actualizarContrasena(Request $request)
    {
        $request->validate([
            'nueva_contrasena' => 'required|min:4',
        ]);

        $documento = session('documento');

        Contrasena::updateOrCreate(
            ['Documento_Empleado' => $documento],
            ['Contrasena_Hash' => $request->nueva_contrasena]
        );

        return back()->with('success', 'Contraseña actualizada correctamente');
    }
}
