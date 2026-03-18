<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Contrasena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerfilController
{
    /* ======================
       PERFIL ADMIN
    ====================== */
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

        $hashActual = hash('sha256', $request->contrasena_actual);
        $contrasenaValida = DB::table('Contrasenas')
            ->where('Documento_Empleado', $documento)
            ->where('Contrasena_Hash', $hashActual)
            ->exists();

        if (!$contrasenaValida) {
            return back()->withInput()->with('error', 'La contraseña actual es incorrecta.');
        }

        $datos = [
            'Nombre_Usuario'     => $request->Nombre_Usuario,
            'Apellido_Usuario'   => $request->Apellido_Usuario,
            'Correo_Electronico' => $request->Correo_Electronico,
            'Telefono'           => $request->Telefono,
        ];

        if ($request->hasFile('Fotos')) {
            $request->validate([
                'Fotos' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            $empleado = Empleado::where('Documento_Empleado', $documento)->first();
            if ($empleado->Fotos && !str_starts_with($empleado->Fotos, 'http')) {
                $rutaAnterior = public_path($empleado->Fotos);
                if (file_exists($rutaAnterior)) unlink($rutaAnterior);
            }

            $archivo    = $request->file('Fotos');
            $nombreFoto = time() . '_' . $documento . '.' . $archivo->getClientOriginalExtension();
            $archivo->move(public_path('fotos_empleados'), $nombreFoto);
            $datos['Fotos'] = 'fotos_empleados/' . $nombreFoto;
        }

        DB::table('Empleados')->where('Documento_Empleado', $documento)->update($datos);

        if ($request->filled('nueva_contrasena')) {
            $request->validate([
                'nueva_contrasena'              => 'min:8',
                'nueva_contrasena_confirmation' => 'required|same:nueva_contrasena',
            ]);

            DB::table('Contrasenas')
                ->where('Documento_Empleado', $documento)
                ->update(['Contrasena_Hash' => $request->nueva_contrasena]);
        }

        session(['nombre' => $request->Nombre_Usuario]);
        if (isset($datos['Fotos'])) {
            session(['foto' => asset($datos['Fotos'])]);
        }

        return back()->with('mensaje', '¡Perfil actualizado correctamente!');
    }

    /* ======================
       PERFIL EMPLEADO
    ====================== */
    public function showEm()
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

        return view('Perfil.PerfilEm', compact('empleado', 'fotoUrl'));
    }

    public function updateEm(Request $request)
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

        $hashActual = hash('sha256', $request->contrasena_actual);
        $contrasenaValida = DB::table('Contrasenas')
            ->where('Documento_Empleado', $documento)
            ->where('Contrasena_Hash', $hashActual)
            ->exists();

        if (!$contrasenaValida) {
            return back()->withInput()->with('error', 'La contraseña actual es incorrecta.');
        }

        $datos = [
            'Nombre_Usuario'     => $request->Nombre_Usuario,
            'Apellido_Usuario'   => $request->Apellido_Usuario,
            'Correo_Electronico' => $request->Correo_Electronico,
            'Telefono'           => $request->Telefono,
        ];

        if ($request->hasFile('Fotos')) {
            $request->validate([
                'Fotos' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            $empleado = Empleado::where('Documento_Empleado', $documento)->first();
            if ($empleado->Fotos && !str_starts_with($empleado->Fotos, 'http')) {
                $rutaAnterior = public_path($empleado->Fotos);
                if (file_exists($rutaAnterior)) unlink($rutaAnterior);
            }

            $archivo    = $request->file('Fotos');
            $nombreFoto = time() . '_' . $documento . '.' . $archivo->getClientOriginalExtension();
            $archivo->move(public_path('fotos_empleados'), $nombreFoto);
            $datos['Fotos'] = 'fotos_empleados/' . $nombreFoto;
        }

        DB::table('Empleados')->where('Documento_Empleado', $documento)->update($datos);

        if ($request->filled('nueva_contrasena')) {
            $request->validate([
                'nueva_contrasena'              => 'min:8',
                'nueva_contrasena_confirmation' => 'required|same:nueva_contrasena',
            ]);

            DB::table('Contrasenas')
                ->where('Documento_Empleado', $documento)
                ->update(['Contrasena_Hash' => $request->nueva_contrasena]);
        }

        session(['nombre' => $request->Nombre_Usuario]);
        if (isset($datos['Fotos'])) {
            session(['foto' => asset($datos['Fotos'])]);
        }

        return back()->with('mensaje', '¡Perfil actualizado correctamente!');
    }
}
