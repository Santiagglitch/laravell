<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class PerfilService
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
        if ($token) $client = $client->withToken($token);
        return $client;
    }

    /**
     * Obtener empleado por documento (consume GET /Empleados y filtra)
     */
    public function obtenerEmpleado(string $documento): ?array
    {
        $response = $this->client()->get($this->baseUrl . '/Empleados');

        if ($response->status() === 401) {
            Session::forget('jwt_token');
            return null;
        }

        if (! $response->successful()) return null;

        $data = $response->json();

        // Si el API devuelve array de strings con ________ (tu formato)
        if (is_array($data) && isset($data[0]) && is_string($data[0])) {
            foreach ($data as $fila) {
                $p = explode('________', $fila);
                if (($p[0] ?? '') === $documento) {
                    return [
                        'Documento_Empleado' => $p[0] ?? '',
                        'Tipo_Documento'     => $p[1] ?? '',
                        'Nombre_Usuario'     => $p[2] ?? '',
                        'Apellido_Usuario'   => $p[3] ?? '',
                        'Edad'               => $p[4] ?? '',
                        'Correo_Electronico' => $p[5] ?? '',
                        'Telefono'           => $p[6] ?? '',
                        'Genero'             => $p[7] ?? '',
                        'ID_Estado'          => $p[8] ?? '',
                        'ID_Rol'             => $p[9] ?? '',
                        'Fotos'              => $p[10] ?? '',
                    ];
                }
            }
            return null;
        }

        // Si viene JSON estructurado
        foreach ($data as $obj) {
            if (isset($obj['Documento_Empleado']) && $obj['Documento_Empleado'] === $documento) {
                return $obj;
            }
        }

        return null;
    }

    /**
     * Obtener contrasena (consume GET /Contrasenas y filtra por documento)
     */
    public function obtenerContrasenaPorDocumento(string $documento): ?array
    {
        $response = $this->client()->get($this->baseUrl . '/Contrasenas');

        if ($response->status() === 401) {
            Session::forget('jwt_token');
            return null;
        }

        if (! $response->successful()) return null;

        $data = $response->json();

        // formato string "ID________Documento________Hash________Fecha"
        if (is_array($data) && isset($data[0]) && is_string($data[0])) {
            foreach ($data as $fila) {
                $p = explode('________', $fila);
                if (($p[1] ?? '') === $documento) {
                    return [
                        'ID_Contrasena'    => $p[0] ?? '',
                        'Documento_Empleado'=> $p[1] ?? '',
                        'Contrasena_Hash'  => $p[2] ?? '',
                        'Fecha_Creacion'   => $p[3] ?? '',
                    ];
                }
            }
            return null;
        }

        // si viene JSON normal
        foreach ($data as $obj) {
            if (isset($obj['Documento_Empleado']) && $obj['Documento_Empleado'] === $documento) {
                return $obj;
            }
        }

        return null;
    }

    /**
     * Actualizar empleado (PUT /EmpleadoActualizar/{Documento_Empleado})
     * $data es array con los campos que quieras actualizar. Si envías 'Fotos' debe ser base64.
     */
    public function actualizarEmpleado(string $documento, array $data): array
    {
        $response = $this->client()->put($this->baseUrl . '/EmpleadoActualizar/' . urlencode($documento), $data);

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
     * Actualizar contraseña (PUT /ActualizarContrasena/{ID_Contrasena})
     * Enviamos Contrasena_Hash (preferible: SHA256 del nuevo password)
     */
    public function actualizarContrasena(string $idContrasena, array $payload): array
    {
        $response = $this->client()->put($this->baseUrl . '/ActualizarContrasena/' . urlencode($idContrasena), $payload);

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
     * Validar login contra API (POST /EmpleadoLogin) — usado para verificar contraseña actual
     */
    public function validarLogin(string $documento, string $contrasenaPlano): bool
    {
        try {
            $response = $this->client()->post($this->baseUrl . '/EmpleadoLogin', [
                'documento' => $documento,
                'contrasena' => $contrasenaPlano,
            ]);

            if ($response->status() === 401) {
                Session::forget('jwt_token');
                return false;
            }

            return str_contains(strtolower($response->body()), 'login correcto');
        } catch (\Throwable $e) {
            return false;
        }
    }
}
