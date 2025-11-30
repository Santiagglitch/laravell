<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ProductosService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.productos.base_url', 'http://localhost:8080'), '/');
    }

    /**
     * Obtener el token JWT de la sesión (lo pone el AuthController al hacer login).
     */
    private function getToken(): ?string
    {
        return Session::get('jwt_token');
    }

    /**
     * Cliente HTTP con (o sin) token.
     */
    private function client()
    {
        $token = $this->getToken();

        $client = Http::acceptJson();

        if ($token) {
            $client = $client->withToken($token); // Authorization: Bearer <token>
        }

        return $client;
    }

    /**
     * GET /Productos
     */
    public function obtenerProductos(): ?array
    {
        $response = $this->client()->get($this->baseUrl . '/Productos');

        // 🔥 si el token está vencido o inválido
        if ($response->status() === 401) {
            Session::forget('jwt_token');
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $data = $response->json();

        // Si la API devuelve un array de strings "ID________Nombre________..."
        if (is_array($data) && isset($data[0]) && is_string($data[0])) {
            $resultado = [];

            foreach ($data as $fila) {
                $partes = explode('________', $fila);

                $resultado[] = [
                    'ID_Producto'     => $partes[0] ?? '',
                    'Nombre_Producto' => $partes[1] ?? '',
                    'Descripcion'     => $partes[2] ?? '',
                    'Precio_Venta'    => $partes[3] ?? '',
                    'Stock_Minimo'    => $partes[4] ?? '',
                    'ID_Categoria'    => $partes[5] ?? '',
                    'ID_Estado'       => $partes[6] ?? '',
                    'ID_Gama'         => $partes[7] ?? '',
                    'Fotos'           => $partes[8] ?? '',
                ];
            }

            return $resultado;
        }

        // Si ya viene como objetos/arrays JSON normales, lo devolvemos tal cual
        return $data;
    }

    /**
     * POST /RegistroP
     */
    public function agregarProducto(array $data): array
    {
        $response = $this->client()->post($this->baseUrl . '/RegistroP', $data);

        // 🔥 token inválido / vencido
        if ($response->status() === 401) {
            Session::forget('jwt_token');
        }

        return [
            'success' => $response->successful(),
            'status'  => $response->status(),
            'body'    => $response->body(),
        ];
    }

    /**
     * PUT /ActualizaProd/{id}
     */
    public function actualizarProducto(string $id, array $data): array
    {
        $response = $this->client()->put($this->baseUrl . '/ActualizaProd/' . urlencode($id), $data);

        // 🔥 token inválido / vencido
        if ($response->status() === 401) {
            Session::forget('jwt_token');
        }

        return [
            'success' => $response->successful(),
            'status'  => $response->status(),
            'body'    => $response->body(),
        ];
    }

    /**
     * DELETE /EliminarPro/{id}
     */
    public function eliminarProducto(string $id): array
    {
        $response = $this->client()->delete($this->baseUrl . '/EliminarPro/' . urlencode($id));

        // 🔥 token inválido / vencido
        if ($response->status() === 401) {
            Session::forget('jwt_token');
        }

        return [
            'success' => $response->successful(),
            'status'  => $response->status(),
            'body'    => $response->body(),
        ];
    }
}
