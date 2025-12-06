<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;

class EmpleadoController
{
    public function get()
    {
        // Trae todos los empleados
        $empleados = Empleado::all();
        return view('empleados.index', compact('empleados'));
    }

    // GUARDAR EMPLEADO NUEVO
public function post(Request $request)
{
    $request->validate([
        'Documento_Empleado'   => 'required|string|max:20',
        'Tipo_Documento'       => 'required|string|max:10',
        'Nombre_Usuario'       => 'required|string|max:50',
        'Apellido_Usuario'     => 'required|string|max:50',
        'Edad'                 => 'required|integer',
        'Correo_Electronico'   => 'required|email',
        'Telefono'             => 'required|string|max:20',
        'Genero'               => 'required|string|max:10',
        'ID_Estado'            => 'required|string|max:10',
        'ID_Rol'               => 'required|string|max:10',
        'Fotos'                => 'nullable|image|max:2048'
    ]);

    // Guardar foto si existe
    $rutaFoto = null;
    if ($request->hasFile('Fotos')) {
        $foto = $request->file('Fotos');
        $nuevoNombre = uniqid() . '_' . $foto->getClientOriginalName();
        $rutaFoto = 'php/fotos_empleados/' . $nuevoNombre;
        $foto->move(public_path('php/fotos_empleados'), $nuevoNombre);
    }

    // Crear empleado
    Empleado::create([
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

    return back()->with('mensaje', 'Empleado registrado correctamente');
}

public function put(Request $request)
{
    // Validar (nota: Fotos ahora es image)
    $validated = $request->validate([
        'Documento_Empleado' => 'required|string|max:20|exists:empleados,Documento_Empleado',
        'Tipo_Documento'     => 'nullable|string|max:10',
        'Nombre_Usuario'     => 'nullable|string|max:30',
        'Apellido_Usuario'   => 'nullable|string|max:30',
        'Edad'               => 'nullable|string|max:20',
        'Correo_Electronico' => 'nullable|string|email|max:100',
        'Telefono'           => 'nullable|string|max:20',
        'Genero'             => 'nullable|in:F,M',
        'ID_Estado'          => 'nullable|string|max:20',
        'ID_Rol'             => 'nullable|string|max:20',
        'Fotos'              => 'nullable|image|max:2048', // ahora es imagen
    ]);

    // Buscar empleado
    $empleado = Empleado::findOrFail($validated['Documento_Empleado']);

    // Quitar la PK
    $datosActualizar = $validated;
    unset($datosActualizar['Documento_Empleado']);

    // Manejo de archivo (si subieron uno)
    if ($request->hasFile('Fotos')) {
        $foto = $request->file('Fotos');

        // Generar nombre único y mover a la misma carpeta que usas en store()
        $nuevoNombre = uniqid() . '_' . $foto->getClientOriginalName();
        $rutaRelativa = 'php/fotos_empleados/' . $nuevoNombre; // misma convención que en store
        $foto->move(public_path('php/fotos_empleados'), $nuevoNombre);

        // Eliminar foto antigua si existe (y no sea null)
        if (!empty($empleado->Fotos)) {
            $rutaAntigua = public_path($empleado->Fotos);
            if (file_exists($rutaAntigua) && is_file($rutaAntigua)) {
                @unlink($rutaAntigua);
            }
        }

        // Actualizar el campo Fotos en los datos a guardar
        $datosActualizar['Fotos'] = $rutaRelativa;
    }

    // Limpia valores null o vacíos
    $datosActualizar = array_filter(
        $datosActualizar,
        fn($value) => !is_null($value) && $value !== ''
    );

    // Actualizar si hay algo que modificar
    if (!empty($datosActualizar)) {
        $empleado->update($datosActualizar);
    }

    return redirect()
        ->route('empleados.index')
        ->with('mensaje', 'Empleado actualizado correctamente.');
}


    // ELIMINAR EMPLEADO
    public function delete(Request $request)
    {
        // Validación
        $validated = $request->validate([
            'Documento_Empleado' => 'required|string|max:20|exists:empleados,Documento_Empleado',
        ]);

        // Buscar y eliminar
        $empleado = Empleado::findOrFail($validated['Documento_Empleado']);
        $empleado->delete();

        return redirect()
            ->route('empleados.index')
            ->with('mensaje', 'Empleado eliminado correctamente.');
    }
}