<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Contrasena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PerfilController
{
    public function mostrar()
    {
        $documento = session('documento');

        if (!$documento) {
            return redirect('/login')->with('error', 'No hay empleado en sesión');
        }

        // Datos del empleado
        $empleado = Empleado::where('Documento_Empleado', $documento)->first();

        // Contraseña del empleado
        $contrasena = Contrasena::where('Documento_Empleado', $documento)->first();

        return view('Perfil.Perfil', compact('empleado', 'contrasena'));
    }

    public function actualizarContrasena(Request $request)
{
    $request->validate([
        'nueva_contrasena' => 'required|min:4',
    ]);

    $documento = session('documento');

    // Generar SHA256 EXACTAMENTE como Java lo valida
    $sha256 = hash('sha256', $request->nueva_contrasena);

    Contrasena::updateOrCreate(
        ['Documento_Empleado' => $documento],
        ['Contrasena_Hash' => $sha256]
    );

    return back()->with('success', 'Contraseña actualizada correctamente');
}

}
