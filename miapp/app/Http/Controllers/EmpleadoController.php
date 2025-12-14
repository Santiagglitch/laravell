<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Contrasena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EmpleadoController
{
    // Listar todos los empleados
    public function get()
    {
        $empleados = Empleado::with('contrasena')->get();
        return view('empleados.index', compact('empleados'));
    }

    // Guardar nuevo empleado (✅ SOLO Laravel inserta en BD)
    public function post(Request $request)
    {
        $request->validate([
            'Documento_Empleado'   => 'required|string|max:20|unique:Empleados,Documento_Empleado',
            'Tipo_Documento'       => 'required|string|max:10',
            'Nombre_Usuario'       => 'required|string|max:30',
            'Apellido_Usuario'     => 'required|string|max:30',
            'Edad'                 => 'required|string|max:20',
            'Correo_Electronico'   => 'required|email|unique:Empleados,Correo_Electronico',
            'Telefono'             => 'required|string|max:10',
            'Genero'               => 'required|in:F,M',
            'ID_Estado'            => 'required|string|max:20',
            'ID_Rol'               => 'required|string|max:20',
            'Fotos'                => 'nullable|image|max:2048',
            'Contrasena'           => 'required|string|min:4',
        ]);

        // 1) Subir foto a Spring (solo archivo) y guardar URL en BD
        $fotoUrl = null;

        if ($request->hasFile('Fotos')) {
            $springBase = rtrim(config('services.spring.base_url', 'http://192.168.80.13:8080'), '/');
            $foto = $request->file('Fotos');

            $resp = Http::asMultipart()
                ->attach('file', file_get_contents($foto->getRealPath()), $foto->getClientOriginalName())
                ->post($springBase . '/upload');


            if (! $resp->successful()) {
                return back()->with('mensaje', 'Error subiendo foto a Spring: ' . $resp->body());
            }

            $fotoUrl = trim($resp->body()); // devuelve la URL en texto
        }

        // 2) Crear empleado SOLO en Laravel
        $empleado = Empleado::create([
            'Documento_Empleado' => $request->Documento_Empleado,
            'Tipo_Documento'     => $request->Tipo_Documento,
            'Nombre_Usuario'     => $request->Nombre_Usuario,
            'Apellido_Usuario'   => $request->Apellido_Usuario,
            'Edad'               => $request->Edad,
            'Correo_Electronico' => $request->Correo_Electronico,
            'Telefono'           => $request->Telefono,
            'Genero'             => $request->Genero,
            'ID_Estado'          => $request->ID_Estado,
            'ID_Rol'             => $request->ID_Rol,
            'Fotos'              => $fotoUrl, // ✅ URL pública de Spring
        ]);

        // 3) Contraseña (como lo tienes)
        Contrasena::create([
            'Documento_Empleado' => $empleado->Documento_Empleado,
            'Contrasena_Hash'    => $request->Contrasena,
        ]);

        return back()->with('mensaje', 'Empleado registrado correctamente');
    }

    // Actualizar empleado (✅ SOLO Laravel actualiza BD)
    public function put(Request $request)
    {
        $request->validate([
            'Documento_Empleado' => 'required|string|max:20|exists:Empleados,Documento_Empleado',
            'Tipo_Documento'     => 'nullable|string|max:10',
            'Nombre_Usuario'     => 'nullable|string|max:30',
            'Apellido_Usuario'   => 'nullable|string|max:30',
            'Edad'               => 'nullable|string|max:20',
            'Correo_Electronico' => 'nullable|string|email|max:100',
            'Telefono'           => 'nullable|string|max:10',
            'Genero'             => 'nullable|in:F,M',
            'ID_Estado'          => 'nullable|string|max:20',
            'ID_Rol'             => 'nullable|string|max:20',
            'Fotos'              => 'nullable|image|max:2048',
            'Contrasena'         => 'nullable|string|min:4',
        ]);

        $empleado = Empleado::findOrFail($request->Documento_Empleado);

        // campos simples
        $empleado->fill($request->except(['Documento_Empleado', 'Contrasena', 'Fotos']));

        // si viene foto -> subir a Spring y guardar URL
        if ($request->hasFile('Fotos')) {
            $springBase = rtrim(config('services.spring.base_url', 'http://192.168.80.13:8080'), '/');
            $foto = $request->file('Fotos');

            $resp = Http::asMultipart()
                ->attach('file', file_get_contents($foto->getRealPath()), $foto->getClientOriginalName())
                ->post($springBase . '/upload');

            if (! $resp->successful()) {
                return redirect()->route('empleados.index')->with('mensaje', 'Error subiendo foto a Spring: ' . $resp->body());
            }

            $empleado->Fotos = trim($resp->body());
        }

        $empleado->save();

        // contraseña
        if (!empty($request->Contrasena)) {
            Contrasena::updateOrCreate(
                ['Documento_Empleado' => $empleado->Documento_Empleado],
                ['Contrasena_Hash' => $request->Contrasena]
            );
        }

        return redirect()->route('empleados.index')->with('mensaje', 'Empleado actualizado correctamente');
    }

    // Eliminar empleado (✅ SOLO Laravel elimina BD)
    public function delete(Request $request)
    {
        $validated = $request->validate([
            'Documento_Empleado' => 'required|string|max:20|exists:Empleados,Documento_Empleado',
        ]);

        $empleado = Empleado::findOrFail($validated['Documento_Empleado']);

        Contrasena::where('Documento_Empleado', $empleado->Documento_Empleado)->delete();
        $empleado->delete();

        return redirect()->route('empleados.index')->with('mensaje', 'Empleado eliminado correctamente');
    }
}
