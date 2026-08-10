<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ConsultaDocumentoService
{
    private string $baseUrl;
    private string $apiKey;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.docs.url', 'http://146.181.39.62:3000'), '/');
        $this->apiKey = (string) config('services.docs.key');
        $this->timeout = (int) config('services.docs.timeout', 10);
    }

    public function consultarDni(string $dni): array
    {
        $dni = preg_replace('/\D+/', '', $dni);

        if (strlen($dni) !== 8) {
            throw new RuntimeException('El DNI debe tener 8 dígitos.');
        }

        if ($this->apiKey === '') {
            throw new RuntimeException('DOCS_API_KEY no está configurado.');
        }

        return $this->get("/api/dni/{$dni}", 'DNI', $dni);
    }

    public function consultarRuc(string $ruc): array
    {
        $ruc = preg_replace('/\D+/', '', $ruc);

        if (strlen($ruc) !== 11) {
            throw new RuntimeException('El RUC debe tener 11 dígitos.');
        }

        if ($this->apiKey === '') {
            throw new RuntimeException('DOCS_API_KEY no está configurado.');
        }

        return $this->get("/api/ruc/{$ruc}", 'RUC', $ruc);
    }

    private function get(string $path, string $tipo, string $numero): array
    {
        try {
            $response = Http::withHeaders(['x-api-key' => $this->apiKey])
                ->acceptJson()
                ->timeout($this->timeout)
                ->connectTimeout(min(5, $this->timeout))
                ->get($this->baseUrl.$path);
        } catch (ConnectionException $e) {
            Log::warning('Conexión fallida con API de documentos', [
                'tipo' => $tipo,
                'numero' => $numero,
                'error' => $e->getMessage(),
            ]);
            throw new RuntimeException('No se pudo conectar al servicio de consultas.');
        }

        if ($response->status() === 404) {
            throw new RuntimeException('No se encontró el '.$tipo.' consultado.');
        }

        if ($response->status() === 400) {
            throw new RuntimeException('El número de '.$tipo.' no es válido.');
        }

        if ($response->status() >= 400) {
            Log::warning('Error en API de documentos', [
                'tipo' => $tipo,
                'numero' => $numero,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new RuntimeException('El servicio de consultas rechazó la solicitud.');
        }

        $data = $response->json();

        if (!is_array($data) || empty($data['encontrado'])) {
            throw new RuntimeException('No se encontró el '.$tipo.' consultado.');
        }

        return $tipo === 'DNI' ? $this->normalizarDni($data) : $this->normalizarRuc($data);
    }

    private function normalizarDni(array $data): array
    {
        $datos = $data['datos'] ?? [];
        $nombre = $this->cleanString(
            $datos['nombreCompleto']
            ?? trim(implode(' ', array_filter([
                $datos['nombres'] ?? null,
                $datos['apellidoPaterno'] ?? null,
                $datos['apellidoMaterno'] ?? null,
            ])))
        );

        if ($nombre === '') {
            throw new RuntimeException('La API no devolvió un nombre para el DNI consultado.');
        }

        return [
            'tipo_documento' => 'DNI',
            'numero_documento' => (string) $data['dni'],
            'nombre_razon_social' => $nombre,
            'direccion' => $this->cleanString($datos['direccion'] ?? null) ?: null,
        ];
    }

    private function normalizarRuc(array $data): array
    {
        $datos = $data['datos'] ?? [];
        $razon = $this->cleanString($data['razonSocial'] ?? $datos['nombreComercial'] ?? null);

        if ($razon === '') {
            throw new RuntimeException('La API no devolvió una razón social para el RUC consultado.');
        }

        $direccion = $this->cleanString($datos['domicilioFiscal'] ?? null);

        return [
            'tipo_documento' => 'RUC',
            'numero_documento' => (string) $data['ruc'],
            'nombre_razon_social' => $razon,
            'direccion' => $direccion !== '' ? $direccion : null,
        ];
    }

    private function cleanString(mixed $value): string
    {
        if (!is_string($value)) {
            return '';
        }

        $trim = trim($value);

        if ($trim === '' || $trim === '-' || $trim === 'null' || $trim === 'NULL') {
            return '';
        }

        if (preg_match('/^"+(.*?)"+$/s', $trim, $matches)) {
            $trim = $matches[1];
        }

        return trim($trim, " \t\n\r\0\x0B\"");
    }
}
