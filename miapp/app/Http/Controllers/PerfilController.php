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

        return view('Perfil.Perfil', compact('empleado', 'contrasena'));
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
