<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Contrasena;
use Illuminate\Http\Request;

class EmpleadoController 
{
    // Listar todos los empleados
    public function get()
    {
        $empleados = Empleado::with('contrasena')->get();
        return view('empleados.index', compact('empleados'));
    }

    // Guardar nuevo empleado
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

        $rutaFoto = null;
        if ($request->hasFile('Fotos')) {
            $foto = $request->file('Fotos');
            $nuevoNombre = uniqid() . '_' . $foto->getClientOriginalName();
            $rutaFoto = 'php/fotos_empleados/' . $nuevoNombre;
            $foto->move(public_path('php/fotos_empleados'), $nuevoNombre);
        }

        // Crear empleado
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
            'Fotos'              => $rutaFoto,
        ]);

        // Crear contraseña
        Contrasena::create([
            'Documento_Empleado' => $empleado->Documento_Empleado,
            'Contrasena_Hash' => $request->Contrasena,
        ]);

        return back()->with('mensaje', 'Empleado registrado correctamente');
    }

    // Actualizar empleado
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

    // Actualizar campos simples
    $empleado->fill($request->except(['Documento_Empleado', 'Contrasena', 'Fotos']));

    // Actualizar foto
    if ($request->hasFile('Fotos')) {
        $foto = $request->file('Fotos');
        $nuevoNombre = uniqid() . '_' . $foto->getClientOriginalName();
        $rutaFoto = 'php/fotos_empleados/' . $nuevoNombre;
        $foto->move(public_path('php/fotos_empleados'), $nuevoNombre);

        // Borrar foto antigua
        if (!empty($empleado->Fotos) && file_exists(public_path($empleado->Fotos))) {
            @unlink(public_path($empleado->Fotos));
        }

        $empleado->Fotos = $rutaFoto;
    }

    $empleado->save();

    // Actualizar contraseña
    if (!empty($request->Contrasena)) {
        Contrasena::updateOrCreate(
            ['Documento_Empleado' => $empleado->Documento_Empleado],
            ['Contrasena_Hash' => $request->Contrasena]
        );
    }

    return redirect()->route('empleados.index')->with('mensaje', 'Empleado actualizado correctamente');
}

    // Eliminar empleado
    public function delete(Request $request)
    {
        $validated = $request->validate([
            'Documento_Empleado' => 'required|string|max:20|exists:Empleados,Documento_Empleado',
        ]);

        $empleado = Empleado::findOrFail($validated['Documento_Empleado']);

        // Borrar foto si existe
        if (!empty($empleado->Fotos) && file_exists(public_path($empleado->Fotos))) {
            @unlink(public_path($empleado->Fotos));
        }

        // Borrar contraseña
        Contrasena::where('Documento_Empleado', $empleado->Documento_Empleado)->delete();

        // Borrar empleado
        $empleado->delete();

        return redirect()->route('empleados.index')->with('mensaje', 'Empleado eliminado correctamente');
    }
}

