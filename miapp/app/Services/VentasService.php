<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VentasService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('AUTH_API_BASE_URL', 'http://localhost:8080');
    }

        public function buscarCliente($documento)
    {
        try {
            Log::info("Buscando cliente: {$documento}");

            $response = Http::timeout(10)
                ->get("{$this->baseUrl}/BuscarPorDocumento/{$documento}");

            Log::info('Respuesta búsqueda cliente:', [
                'status' => $response->status(),
                'body'   => $response->body()
            ]);

            if ($response->successful() && $response->json()) {
                $data = $response->json();

                // Normalizar claves para asegurar mayúsculas correctas
                return [
                    'Documento_Cliente' => $data['Documento_Cliente'] ?? $data['documento_Cliente'] ?? null,
                    'Nombre_Cliente'    => $data['Nombre_Cliente']    ?? $data['nombre_Cliente']    ?? null,
                    'Apellido_Cliente'  => $data['Apellido_Cliente']  ?? $data['apellido_Cliente']  ?? null,
                    'ID_Estado'         => $data['ID_Estado']         ?? $data['id_Estado']         ?? null,
                ];
            }

            return null;

        } catch (\Exception $e) {
            Log::error("Error buscando cliente: " . $e->getMessage());
            return null;
        }
    }

    public function crearCliente($datos)
    {
        try {
            Log::info('Intentando crear cliente', $datos);

            $response = Http::timeout(10)
                ->post("{$this->baseUrl}/RegistraC", [
                    'Documento_Cliente' => $datos['Documento_Cliente'],
                    'Nombre_Cliente'    => $datos['Nombre_Cliente'],
                    'Apellido_Cliente'  => $datos['Apellido_Cliente'],
                    'ID_Estado'         => $datos['ID_Estado']
                ]);

            Log::info('Respuesta crear cliente:', [
                'status' => $response->status(),
                'body'   => $response->body()
            ]);

            if ($response->successful()) {
                Log::info('Cliente creado exitosamente');
                return true;
            }

            Log::error('API retornó error al crear cliente:', [
                'status' => $response->status(),
                'body'   => $response->body()
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error("Error creando cliente: " . $e->getMessage());
            return false;
        }
    }

    public function crearVenta($datos)
    {
        try {
            Log::info('Intentando crear venta', $datos);

            $response = Http::timeout(10)
                ->post("{$this->baseUrl}/VentaRegistro", [
                    'Documento_Cliente'  => $datos['Documento_Cliente'],
                    'Documento_Empleado' => $datos['Documento_Empleado']
                ]);

            Log::info('Respuesta de API:', [
                'status' => $response->status(),
                'body'   => $response->body()
            ]);

            if ($response->successful()) {
                Log::info('Venta creada exitosamente');
                return true;
            }

            Log::error('API retornó error al crear venta:', [
                'status' => $response->status(),
                'body'   => $response->body()
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error("Error creando venta: " . $e->getMessage());
            return false;
        }
    }
}