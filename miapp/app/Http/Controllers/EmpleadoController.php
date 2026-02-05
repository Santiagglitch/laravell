<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Contrasena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Estado;
use App\Models\Rol;

class EmpleadoController
{
    public function get()
    {
        // ✅ CAMBIO NECESARIO: cargar estado y rol para poder mostrar el NOMBRE en la vista
        $empleados = Empleado::with(['contrasena', 'estado', 'rol'])->get();
        return view('empleados.index', compact('empleados'));
    }

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

            // ✅ CAMBIO NECESARIO (eran string)
            'ID_Estado'            => 'required|integer|exists:Estados,ID_Estado',
            'ID_Rol'               => 'required|integer|exists:Roles,ID_Rol',

            'Fotos'                => 'nullable|image|max:2048',
            'Contrasena'           => 'required|string|min:4',
        ]);

        $fotoUrl = null;

        if ($request->hasFile('Fotos')) {
            $springBase = rtrim(config('services.spring.base_url', 'http://192.168.1.190:8080'), '/');
            $foto = $request->file('Fotos');

            $resp = Http::asMultipart()
                ->attach('file', file_get_contents($foto->getRealPath()), $foto->getClientOriginalName())
                ->post($springBase . '/upload');

            if (! $resp->successful()) {
                return back()->with('mensaje', 'Error subiendo foto a Spring: ' . $resp->body());
            }

            $fotoUrl = trim($resp->body());
        }


        $empleado = Empleado::create([
            'Documento_Empleado' => $request->Documento_Empleado,
            'Tipo_Documento'     => $request->Tipo_Documento,
            'Nombre_Usuario'     => $request->Nombre_Usuario,
            'Apellido_Usuario'   => $request->Apellido_Usuario,
            'Edad'               => $request->Edad,
            'Correo_Electronico' => $request->Correo_Electronico,
            'Telefono'           => $request->Telefono,
            'Genero'             => $request->Genero,

            // ✅ CAMBIO NECESARIO (asegurar int)
            'ID_Estado'          => (int) $request->ID_Estado,
            'ID_Rol'             => (int) $request->ID_Rol,

            'Fotos'              => $fotoUrl,
        ]);

        Contrasena::create([
            'Documento_Empleado' => $empleado->Documento_Empleado,
            'Contrasena_Hash'    => $request->Contrasena,
        ]);

        return back()->with('mensaje', 'Empleado registrado correctamente');
    }

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

            // ✅ CAMBIO NECESARIO (eran string)
            'ID_Estado'          => 'nullable|integer|exists:Estados,ID_Estado',
            'ID_Rol'             => 'nullable|integer|exists:Roles,ID_Rol',

            'Fotos'              => 'nullable|image|max:2048',
            'Contrasena'         => 'nullable|string|min:4',
        ]);

        $empleado = Empleado::findOrFail($request->Documento_Empleado);

        $empleado->fill($request->except(['Documento_Empleado', 'Contrasena', 'Fotos']));

        // ✅ CAMBIO NECESARIO (asegurar int si vienen)
        if (!is_null($request->ID_Estado)) $empleado->ID_Estado = (int) $request->ID_Estado;
        if (!is_null($request->ID_Rol))    $empleado->ID_Rol    = (int) $request->ID_Rol;

        if ($request->hasFile('Fotos')) {
            $springBase = rtrim(config('services.spring.base_url', 'http://10.197.10.210:8080'), '/');
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

        if (!empty($request->Contrasena)) {
            Contrasena::updateOrCreate(
                ['Documento_Empleado' => $empleado->Documento_Empleado],
                ['Contrasena_Hash' => $request->Contrasena]
            );
        }

        return redirect()->route('empleados.index')->with('mensaje', 'Empleado actualizado correctamente');
    }

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
