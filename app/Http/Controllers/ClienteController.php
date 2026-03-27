<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ClienteController
{
    public function get()
    {
        $clientes = Cliente::all();
        return view('clientes.index', compact('clientes'));
    }

    public function post(Request $request)
    {
        try {
            $validated = $request->validate([
                'Documento_Cliente' => 'required|string|max:20|unique:Clientes,Documento_Cliente',
                'Nombre_Cliente'    => 'required|string|max:20',
                'Apellido_Cliente'  => 'required|string|max:20',
                'ID_Estado'         => 'required|integer|in:1,2',
            ]);

            Cliente::create($validated);

            return redirect()
                ->route('clientes.index')
                ->with('mensaje', 'Cliente registrado correctamente.');
                
        } catch (ValidationException $e) {
            if (isset($e->errors()['Documento_Cliente'])) {
                return redirect()
                    ->route('clientes.index')
                    ->with('error', 'El cliente con este documento ya está registrado.');
            }
            throw $e;
        }
    }

    public function put(Request $request)
    {
        $validated = $request->validate([
            'Documento_Cliente' => 'required|string|max:20|exists:Clientes,Documento_Cliente',
            'Nombre_Cliente'    => 'nullable|string|max:20',
            'Apellido_Cliente'  => 'nullable|string|max:20',
            'ID_Estado'         => 'nullable|integer|in:1,2',
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
        	'Documento_Cliente' => 'required|string|max:20|exists:Clientes,Documento_Cliente',
    	]);
    		$cliente = Cliente::findOrFail($validated['Documento_Cliente']);
    	try {
        	$cliente->delete();
        	return redirect()
            	->route('clientes.index')
            	->with('mensaje', 'Cliente eliminado correctamente.');
    	} catch (\Exception $e) {
        	return redirect()
       		->route('clientes.index')
       		->with('error', 'No puedes eliminar este cliente porque tiene ventas asociadas.');
    		}
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
    $validated = $request->validate([
        'Documento_Cliente' => 'required|string|max:20|exists:Clientes,Documento_Cliente',
    ]);
    $cliente = Cliente::findOrFail($validated['Documento_Cliente']);
    try {
        $cliente->delete();
        return redirect()
            ->route('clientes.indexEm')
            ->with('mensaje', 'Cliente eliminado correctamente.');
    } catch (\Exception $e) {
        return redirect()
            ->route('clientes.indexEm')
            ->with('error', 'No puedes eliminar este cliente porque tiene ventas asociadas.');
    }
}
}
