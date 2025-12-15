<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController
{
    public function get()
    {
        $clientes = Cliente::all();
        return view('clientes.index', compact('clientes'));
    }

    public function post(Request $request)
    {
        $validated = $request->validate([
            'Documento_Cliente' => 'required|string|max:20|unique:clientes,Documento_Cliente',
            'Nombre_Cliente'    => 'required|string|max:20',
            'Apellido_Cliente'  => 'nullable|string|max:20',
            'Telefono'          => 'nullable|string|max:15',
            'Fecha_Nacimiento'  => 'nullable|date',
            'Genero'            => 'required|in:F,M',
            'ID_Estado'         => 'required|in:EST001,EST002,EST003',
        ]);

        Cliente::create($validated);

        return redirect()
            ->route('clientes.index')
            ->with('mensaje', 'Cliente registrado correctamente.');
    }

    public function put(Request $request)
    {
        $validated = $request->validate([
            'Documento_Cliente' => 'required|string|max:20|exists:clientes,Documento_Cliente',
            'Nombre_Cliente'    => 'nullable|string|max:20',
            'Apellido_Cliente'  => 'nullable|string|max:20',
            'Telefono'          => 'nullable|string|max:15',
            'Fecha_Nacimiento'  => 'nullable|date',
            'Genero'            => 'nullable|in:F,M',
            'ID_Estado'         => 'nullable|in:EST001,EST002,EST003',
        ]);

        $cliente = Cliente::findOrFail($validated['Documento_Cliente']);

        $datosActualizar = $validated;
        unset($datosActualizar['Documento_Cliente']);

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

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'Documento_Cliente' => 'required|string|max:20|exists:clientes,Documento_Cliente',
        ]);

        $cliente = Cliente::findOrFail($validated['Documento_Cliente']);
        $cliente->delete();

        return redirect()
            ->route('clientes.index')
            ->with('mensaje', 'Cliente eliminado correctamente.');
    }

    public function indexEmpleado()
    {
        $clientes = Cliente::all();
        return view('clientes.indexEm', compact('clientes'));
    }

    public function storeEmpleado(Request $request)
    {
        $this->post($request);
        return redirect()->route('clientes.indexEm')->with('mensaje', 'Cliente creado correctamente.');
    }

    public function updateEmpleado(Request $request)
    {
        $this->put($request);
        return redirect()->route('clientes.indexEm')->with('mensaje', 'Cliente actualizado correctamente.');
    }

    public function destroyEmpleado(Request $request)
    {
        $this->delete($request);
        return redirect()->route('clientes.indexEm')->with('mensaje', 'Cliente eliminado correctamente.');
    }
}
