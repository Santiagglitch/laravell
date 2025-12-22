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

  
    private function getToken(): ?string
    {
        return Session::get('jwt_token');
    }

    private function client()
    {
        $token = $this->getToken();

        $client = Http::acceptJson();

        if ($token) {
            $client = $client->withToken($token);
        }

        return $client;
    }

 
    public function obtenerProductos(): ?array
    {
        $response = $this->client()->get($this->baseUrl . '/Productos');

        if ($response->status() === 401) {
            Session::forget('jwt_token');
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $data = $response->json();

        if (is_array($data) && isset($data[0]) && is_string($data[0])) {
            $resultado = [];

            foreach ($data as $fila) {
                $p = explode('________', $fila);

                $resultado[] = [
                    'ID_Producto'     => $p[0] ?? '',
                    'Nombre_Producto' => $p[1] ?? '',
                    'Descripcion'     => $p[2] ?? '',
                    'Precio_Venta'    => $p[3] ?? '',
                    'Stock_Minimo'    => $p[4] ?? '',
                    'ID_Categoria'    => $p[5] ?? '',
                    'ID_Estado'       => $p[6] ?? '',
                    'ID_Gama'         => $p[7] ?? '',
                    'Fotos'           => $p[8] ?? '', 
                ];
            }

            return $resultado;
        }

        return $data;
    }

   
    public function agregarProducto(array $data): array
    {
        $response = $this->client()->post($this->baseUrl . '/RegistroP', $data);

        if ($response->status() === 401) {
            Session::forget('jwt_token');
        }

        return [
            'success' => $response->successful(),
            'status'  => $response->status(),
            'body'    => $response->body(),
        ];
    }

    public function actualizarProducto(string $id, array $data): array
    {
        $response = $this->client()->put(
            $this->baseUrl . '/ActualizaProd/' . urlencode($id),
            $data
        );

        if ($response->status() === 401) {
            Session::forget('jwt_token');
        }

        return [
            'success' => $response->successful(),
            'status'  => $response->status(),
            'body'    => $response->body(),
        ];
    }


    public function eliminarProducto(string $id): array
    {
        $response = $this->client()->delete(
            $this->baseUrl . '/EliminarPro/' . urlencode($id)
        );

        if ($response->status() === 401) {
            Session::forget('jwt_token');
        }

        return [
            'success' => $response->successful(),
            'status'  => $response->status(),
            'body'    => $response->body(),
        ];
    }

    public function agregarProductoMultipart(array $data, $file): array
    {
        $response = Http::asMultipart()
            ->withToken($this->getToken())
            ->attach(
                'file',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )
            ->post($this->baseUrl . '/RegistroPMultipart', [
                [
                    'name' => 'data',
                    'contents' => json_encode([
                        'ID_Producto'     => $data['ID_Producto'],
                        'Nombre_Producto' => $data['Nombre_Producto'],
                        'Descripcion'     => $data['Descripcion'] ?? '',
                        'Precio_Venta'    => (string)($data['Precio_Venta'] ?? ''),
                        'Stock_Minimo'    => (string)($data['Stock_Minimo'] ?? ''),
                        'ID_Categoria'    => $data['ID_Categoria'] ?? '',
                        'ID_Estado'       => $data['ID_Estado'] ?? '',
                        'ID_Gama'         => $data['ID_Gama'] ?? '',
                        'Fotos'           => ''
                    ])
                ]
            ]);

        if ($response->status() === 401) {
            Session::forget('jwt_token');
        }

        return [
            'success' => $response->successful(),
            'status'  => $response->status(),
            'body'    => $response->body(),
        ];
    }


    public function actualizarProductoMultipart(string $id, array $data, $file): array
    {
        $response = Http::asMultipart()
            ->withToken($this->getToken())
            ->attach(
                'file',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )
            ->put($this->baseUrl . '/ActualizaProdMultipart/' . urlencode($id), [
                [
                    'name' => 'data',
                    'contents' => json_encode([
                        'Nombre_Producto' => $data['Nombre_Producto'] ?? '',
                        'Descripcion'     => $data['Descripcion'] ?? '',
                        'Precio_Venta'    => (string)($data['Precio_Venta'] ?? ''),
                        'Stock_Minimo'    => (string)($data['Stock_Minimo'] ?? ''),
                        'ID_Categoria'    => $data['ID_Categoria'] ?? '',
                        'ID_Estado'       => $data['ID_Estado'] ?? '',
                        'ID_Gama'         => $data['ID_Gama'] ?? '',
                        'Fotos'           => ''
                    ])
                ]
            ]);

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