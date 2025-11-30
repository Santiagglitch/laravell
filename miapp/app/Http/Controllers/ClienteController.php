<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        // Trae todos los clientes
        $clientes = Cliente::all();
        return view('clientes.index', compact('clientes'));
    }

    // GUARDAR CLIENTE NUEVO
    public function store(Request $request)
    {
        // Validar datos del formulario
        $validated = $request->validate([
            'Documento_Cliente' => 'required|string|max:20|unique:clientes,Documento_Cliente',
            'Nombre_Cliente'    => 'required|string|max:20',
            'Apellido_Cliente'  => 'nullable|string|max:20',
            'Telefono'          => 'nullable|string|max:15',
            'Fecha_Nacimiento'  => 'nullable|date',
            'Genero'            => 'required|in:F,M',
            'ID_Estado'         => 'required|in:EST001,EST002,EST003',
        ]);

        // Crear el cliente en la BD
        Cliente::create($validated);

        // Redirigir de nuevo a /clientes con un mensaje
        return redirect()
            ->route('clientes.index')
            ->with('mensaje', 'Cliente registrado correctamente.');
    }

    // ACTUALIZAR CLIENTE
    public function update(Request $request)
    {
        // Validar datos
        $validated = $request->validate([
            'Documento_Cliente' => 'required|string|max:20|exists:clientes,Documento_Cliente',
            'Nombre_Cliente'    => 'nullable|string|max:20',
            'Apellido_Cliente'  => 'nullable|string|max:20',
            'Telefono'          => 'nullable|string|max:15',
            'Fecha_Nacimiento'  => 'nullable|date',
            'Genero'            => 'nullable|in:F,M',
            'ID_Estado'         => 'nullable|in:EST001,EST002,EST003',
        ]);

        // Buscar el cliente por Documento_Cliente
        $cliente = Cliente::findOrFail($validated['Documento_Cliente']);

        // Quitar la PK de los datos a actualizar
        $datosActualizar = $validated;
        unset($datosActualizar['Documento_Cliente']);

        // Quitar valores null o vacíos para no sobrescribir con nada
        $datosActualizar = array_filter(
            $datosActualizar,
            fn($value) => !is_null($value) && $value !== ''
        );

        if (!empty($datosActualizar)) {
            $cliente->update($datosActualizar);
        }

        return redirect()
            ->route('clientes.index')
            ->with('mensaje', 'Cliente actualizado correctamente.');
    }

    // 🆕 ELIMINAR CLIENTE
    public function destroy(Request $request)
    {
        // Validar que nos manden un documento que exista
        $validated = $request->validate([
            'Documento_Cliente' => 'required|string|max:20|exists:clientes,Documento_Cliente',
        ]);

        // Buscar y eliminar
        $cliente = Cliente::findOrFail($validated['Documento_Cliente']);
        $cliente->delete();

        return redirect()
            ->route('clientes.index')
            ->with('mensaje', 'Cliente eliminado correctamente.');
    }
}
