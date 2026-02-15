<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Contrasena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerfilController
{
    public function mostrar()
    {
        $documento = session('documento');

        if (!$documento) {
            return redirect('/login')->with('error', 'No hay empleado en sesión');
        }

        $empleado = Empleado::where('Documento_Empleado', $documento)->first();

        $fotoUrl = null;
        if ($empleado && $empleado->Fotos) {
            $foto = trim($empleado->Fotos);
            $fotoUrl = str_starts_with($foto, 'http')
                ? $foto
                : asset($foto);
        }

        return view('Perfil.Perfil', compact('empleado', 'fotoUrl'));
    }

    public function actualizar(Request $request)
    {
        $documento = session('documento');

        $request->validate([
            'Nombre_Usuario'      => 'required|string|max:100',
            'Apellido_Usuario'    => 'required|string|max:100',
            'Correo_Electronico'  => 'required|email|max:100',
            'Telefono'            => 'required|string|max:20',
            'contrasena_actual'   => 'required|string',
        ], [
            'Nombre_Usuario.required'     => 'El nombre es obligatorio.',
            'Apellido_Usuario.required'   => 'El apellido es obligatorio.',
            'Correo_Electronico.required' => 'El correo es obligatorio.',
            'Correo_Electronico.email'    => 'El correo no tiene un formato válido.',
            'Telefono.required'           => 'El teléfono es obligatorio.',
            'contrasena_actual.required'  => 'Debes ingresar tu contraseña actual para guardar cambios.',
        ]);

        // Verificar contraseña actual
        $hashActual = hash('sha256', $request->contrasena_actual);$contrasenaValida = DB::table('Contrasenas')
        ->where('Documento_Empleado', $documento)->where('Contrasena_Hash', $hashActual)->exists();

        if (!$contrasenaValida) {
            return back()
                ->withInput()
                ->with('error', 'La contraseña actual es incorrecta.');
        }

        $datos = [
            'Nombre_Usuario'     => $request->Nombre_Usuario,
            'Apellido_Usuario'   => $request->Apellido_Usuario,
            'Correo_Electronico' => $request->Correo_Electronico,
            'Telefono'           => $request->Telefono,
        ];

        // Manejo de foto
        if ($request->hasFile('Fotos')) {
            $request->validate([
                'Fotos' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            ], [
                'Fotos.image'   => 'El archivo debe ser una imagen.',
                'Fotos.mimes'   => 'Formatos permitidos: JPG, PNG, WEBP.',
                'Fotos.max'     => 'La imagen no debe superar 2MB.',
            ]);

            // Eliminar foto anterior si existe en Laravel
            $empleado = Empleado::where('Documento_Empleado', $documento)->first();
            if ($empleado->Fotos && !str_starts_with($empleado->Fotos, 'http')) {
                $rutaAnterior = public_path($empleado->Fotos);
                if (file_exists($rutaAnterior)) {
                    unlink($rutaAnterior);
                }
            }

            $archivo    = $request->file('Fotos');
            $nombreFoto = time() . '_' . $documento . '.' . $archivo->getClientOriginalExtension();
            $archivo->move(public_path('fotos_empleados'), $nombreFoto);
            $datos['Fotos'] = 'fotos_empleados/' . $nombreFoto;
        }

        // Actualizar datos del empleado
        DB::table('Empleados')
            ->where('Documento_Empleado', $documento)
            ->update($datos);

        // Actualizar contraseña nueva si se ingresó
        if ($request->filled('nueva_contrasena')) {
            $request->validate([
                'nueva_contrasena'              => 'min:8',
                'nueva_contrasena_confirmation' => 'required|same:nueva_contrasena',
            ], [
                'nueva_contrasena.min'                       => 'La nueva contraseña debe tener al menos 8 caracteres.',
                'nueva_contrasena_confirmation.same'         => 'Las contraseñas no coinciden.',
                'nueva_contrasena_confirmation.required'     => 'Debes confirmar la nueva contraseña.',
            ]);

           DB::table('Contrasenas')->where('Documento_Empleado', $documento)->update(['Contrasena_Hash' => $request->nueva_contrasena]);
        }

        // Actualizar sesión con nuevo nombre
        session(['nombre' => $request->Nombre_Usuario]);

        // Actualizar foto en sesión si se cambió
        if (isset($datos['Fotos'])) {
            session(['foto' => asset($datos['Fotos'])]);
        }

        return back()->with('mensaje', '¡Perfil actualizado correctamente!');
    }
}