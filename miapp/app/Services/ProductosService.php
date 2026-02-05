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

    // =========================
    // ✅ construir mapas id => nombre desde tu API Spring
    // =========================
    private function construirMapaDesdeListaStrings(?array $data): array
    {
        $map = [];

        if (!is_array($data)) return $map;

        foreach ($data as $fila) {
            if (!is_string($fila)) continue;

            $p = explode('________', $fila);

            $a = isset($p[0]) ? trim($p[0]) : '';
            $b = isset($p[1]) ? trim($p[1]) : '';

            if ($a === '' || $b === '') continue;

            if (ctype_digit($a)) {
                $map[(string)$a] = $b;
                continue;
            }

            if (ctype_digit($b)) {
                $map[(string)$b] = $a;
                continue;
            }

            $map[(string)$a] = $b;
        }

        return $map;
    }

    private function obtenerEstadosMap(): array
    {
        $response = $this->client()->get($this->baseUrl . '/Estado');

        if ($response->status() === 401) {
            Session::forget('jwt_token');
            return [];
        }

        if (! $response->successful()) return [];

        return $this->construirMapaDesdeListaStrings($response->json());
    }

    private function obtenerCategoriasMap(): array
    {
        $response = $this->client()->get($this->baseUrl . '/Categorias');

        if ($response->status() === 401) {
            Session::forget('jwt_token');
            return [];
        }

        if (! $response->successful()) return [];

        return $this->construirMapaDesdeListaStrings($response->json());
    }

    private function obtenerGamasMap(): array
    {
        $response = $this->client()->get($this->baseUrl . '/Gamas');

        if ($response->status() === 401) {
            Session::forget('jwt_token');
            return [];
        }

        if (! $response->successful()) return [];

        return $this->construirMapaDesdeListaStrings($response->json());
    }

    // =========================
    // ✅ Catálogos para selects
    // =========================
    public function obtenerCatalogos(): array
    {
        return [
            'categorias' => $this->obtenerCategoriasMap(),
            'estados'    => $this->obtenerEstadosMap(),
            'gamas'      => $this->obtenerGamasMap(),
        ];
    }

    // =========================
    // ✅ obtenerProductos agrega nombres
    // =========================
    public function obtenerProductos(): ?array
    {
        $response = $this->client()->get($this->baseUrl . '/Productos');

        if ($response->status() === 401) {
            Session::forget('jwt_token');
            return null;
        }

        if (! $response->successful()) return null;

        $data = $response->json();

        $mapEstados    = $this->obtenerEstadosMap();
        $mapCategorias = $this->obtenerCategoriasMap();
        $mapGamas      = $this->obtenerGamasMap();

        if (is_array($data) && isset($data[0]) && is_string($data[0])) {
            $resultado = [];

            foreach ($data as $fila) {
                $p = explode('________', $fila);

                $idCategoria = (string)($p[5] ?? '');
                $idEstado    = (string)($p[6] ?? '');
                $idGama      = (string)($p[7] ?? '');

                $resultado[] = [
                    'ID_Producto'     => $p[0] ?? '',
                    'Nombre_Producto' => $p[1] ?? '',
                    'Descripcion'     => $p[2] ?? '',
                    'Precio_Venta'    => $p[3] ?? '',
                    'Stock_Minimo'    => $p[4] ?? '',

                    'ID_Categoria'    => $idCategoria,
                    'ID_Estado'       => $idEstado,
                    'ID_Gama'         => $idGama,

                    'Categoria'       => $mapCategorias[$idCategoria] ?? $idCategoria,
                    'Estado'          => $mapEstados[$idEstado] ?? $idEstado,
                    'Gama'            => $mapGamas[$idGama] ?? $idGama,

                    'Fotos'           => $p[8] ?? '',
                ];
            }

            return $resultado;
        }

        return $data;
    }

    // ✅ Crear normal (JSON). OJO: no mandar ID_Producto
    public function agregarProducto(array $data): array
    {
        unset($data['ID_Producto']); // ✅ importante

        // ✅ tipado recomendado
        if (isset($data['Precio_Venta']) && $data['Precio_Venta'] !== '') $data['Precio_Venta'] = (float) $data['Precio_Venta'];
        if (isset($data['Stock_Minimo']) && $data['Stock_Minimo'] !== '') $data['Stock_Minimo'] = (int) $data['Stock_Minimo'];

        $response = $this->client()->post($this->baseUrl . '/RegistroP', $data);

        if ($response->status() === 401) Session::forget('jwt_token');

        return [
            'success' => $response->successful(),
            'status'  => $response->status(),
            'body'    => $response->body(),
        ];
    }

    public function actualizarProducto(int $id, array $data): array
    {
        // ✅ tipado recomendado
        if (isset($data['Precio_Venta']) && $data['Precio_Venta'] !== '') $data['Precio_Venta'] = (float) $data['Precio_Venta'];
        if (isset($data['Stock_Minimo']) && $data['Stock_Minimo'] !== '') $data['Stock_Minimo'] = (int) $data['Stock_Minimo'];

        $response = $this->client()->put(
            $this->baseUrl . '/ActualizaProd/' . urlencode((string)$id),
            $data
        );

        if ($response->status() === 401) Session::forget('jwt_token');

        return [
            'success' => $response->successful(),
            'status'  => $response->status(),
            'body'    => $response->body(),
        ];
    }

    public function eliminarProducto(int $id): array
    {
        $response = $this->client()->delete(
            $this->baseUrl . '/EliminarPro/' . urlencode((string)$id)
        );

        if ($response->status() === 401) Session::forget('jwt_token');

        return [
            'success' => $response->successful(),
            'status'  => $response->status(),
            'body'    => $response->body(),
        ];
    }

    public function agregarProductoMultipart(array $data, $file): array
    {
        // ✅ importante: NO enviar ID_Producto
        unset($data['ID_Producto']);

        // ✅ tipado correcto (DTO: Double/Integer)
        if (isset($data['Precio_Venta']) && $data['Precio_Venta'] !== '') $data['Precio_Venta'] = (float) $data['Precio_Venta'];
        if (isset($data['Stock_Minimo']) && $data['Stock_Minimo'] !== '') $data['Stock_Minimo'] = (int) $data['Stock_Minimo'];

        if (isset($data['ID_Categoria']) && $data['ID_Categoria'] !== '') $data['ID_Categoria'] = (int) $data['ID_Categoria'];
        if (isset($data['ID_Estado'])    && $data['ID_Estado']    !== '') $data['ID_Estado']    = (int) $data['ID_Estado'];
        if (isset($data['ID_Gama'])      && $data['ID_Gama']      !== '') $data['ID_Gama']      = (int) $data['ID_Gama'];

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
                        'Nombre_Producto' => $data['Nombre_Producto'],
                        'Descripcion'     => $data['Descripcion'] ?? '',
                        'Precio_Venta'    => $data['Precio_Venta'] ?? null,
                        'Stock_Minimo'    => $data['Stock_Minimo'] ?? null,
                        'ID_Categoria'    => $data['ID_Categoria'] ?? null,
                        'ID_Estado'       => $data['ID_Estado'] ?? null,
                        'ID_Gama'         => $data['ID_Gama'] ?? null,
                        'Fotos'           => ''
                    ])
                ]
            ]);

        if ($response->status() === 401) Session::forget('jwt_token');

        return [
            'success' => $response->successful(),
            'status'  => $response->status(),
            'body'    => $response->body(),
        ];
    }

    public function actualizarProductoMultipart(int $id, array $data, $file): array
    {
        // ✅ tipado correcto
        if (isset($data['Precio_Venta']) && $data['Precio_Venta'] !== '') $data['Precio_Venta'] = (float) $data['Precio_Venta'];
        if (isset($data['Stock_Minimo']) && $data['Stock_Minimo'] !== '') $data['Stock_Minimo'] = (int) $data['Stock_Minimo'];

        if (isset($data['ID_Categoria']) && $data['ID_Categoria'] !== '') $data['ID_Categoria'] = (int) $data['ID_Categoria'];
        if (isset($data['ID_Estado'])    && $data['ID_Estado']    !== '') $data['ID_Estado']    = (int) $data['ID_Estado'];
        if (isset($data['ID_Gama'])      && $data['ID_Gama']      !== '') $data['ID_Gama']      = (int) $data['ID_Gama'];

        $response = Http::asMultipart()
            ->withToken($this->getToken())
            ->attach(
                'file',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )
            ->put($this->baseUrl . '/ActualizaProdMultipart/' . urlencode((string)$id), [
                [
                    'name' => 'data',
                    'contents' => json_encode([
                        'Nombre_Producto' => $data['Nombre_Producto'] ?? '',
                        'Descripcion'     => $data['Descripcion'] ?? '',
                        'Precio_Venta'    => $data['Precio_Venta'] ?? null,
                        'Stock_Minimo'    => $data['Stock_Minimo'] ?? null,
                        'ID_Categoria'    => $data['ID_Categoria'] ?? null,
                        'ID_Estado'       => $data['ID_Estado'] ?? null,
                        'ID_Gama'         => $data['ID_Gama'] ?? null,
                        'Fotos'           => ''
                    ])
                ]
            ]);

        if ($response->status() === 401) Session::forget('jwt_token');

        return [
            'success' => $response->successful(),
            'status'  => $response->status(),
            'body'    => $response->body(),
        ];
    }
}
