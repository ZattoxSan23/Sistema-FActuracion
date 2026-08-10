<?php

namespace App\Services\Sunat;

use App\Models\Comprobante;
use App\Models\Empresa;
use App\Models\SunatConfig;
use App\Models\SunatRespuesta;
use App\Models\Venta;
use Illuminate\Support\Facades\Log;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

/**
 * Servicio principal para integración con SUNAT.
 *
 * Implementa:
 * - Generación de XML UBL 2.1 (Boletas, Facturas, Notas)
 * - Firma digital con XMLDSig
 * - Envío a SUNAT vía SOAP (GRE) o REST (OSE)
 * - Recepción de CDR
 * - Comunicación de baja
 */
class SunatService
{
    private ?SunatConfig $config;
    private ?Empresa $empresa;

    public function __construct()
    {
        $this->config = SunatConfig::actual();
        $this->empresa = Empresa::actual();
    }

    /**
     * Genera XML, firma y envía a SUNAT.
     */
    public function generarYEnviar(Venta $venta, Comprobante $comprobante): void
    {
        try {
            // 1. Generar XML UBL 2.1
            $generador = new XmlGenerator($venta);
            $xml = $generador->generar();

            $comprobante->update([
                'xml_sin_firma' => $xml,
                'estado' => Comprobante::ESTADO_BORRADOR,
            ]);

            // 2. Firmar XML
            if (!$this->config) {
                throw new \RuntimeException('No hay configuración de SUNAT');
            }

            $xmlFirmado = $this->firmarXml($xml);
            $hash = $this->generarHashCpe($xmlFirmado);

            $comprobante->update([
                'xml_firmado' => $xmlFirmado,
                'hash_cpe' => $hash,
                'estado' => Comprobante::ESTADO_FIRMADO,
                'fecha_firma' => now(),
            ]);

            // 3. Enviar a SUNAT si está configurado el envío automático
            if ($this->config->envio_automatico) {
                $this->enviarASunat($comprobante);
            }
        } catch (\Exception $e) {
            Log::error('Error en SUNAT: ' . $e->getMessage(), [
                'venta_id' => $venta->id,
                'comprobante_id' => $comprobante->id,
            ]);

            $comprobante->update([
                'estado' => Comprobante::ESTADO_EXCEPCION,
                'descripcion_respuesta' => $e->getMessage(),
            ]);

            $venta->update(['estado_sunat' => 'excepcion']);
        }
    }

    /**
     * Firma el XML usando XMLDSig con el certificado de SUNAT.
     */
    public function firmarXml(string $xml): string
    {
        $certPath = $this->config->certificado_path ?? storage_path('sunat/certificate.pem');

        if (!file_exists($certPath)) {
            throw new \RuntimeException("Certificado no encontrado en: {$certPath}");
        }

        $certContent = file_get_contents($certPath);
        $password = $this->config->certificado_password ?? '';

        $certInfo = openssl_pkcs12_read($certContent, $certs, $password);
        if (!$certInfo) {
            throw new \RuntimeException('No se pudo leer el certificado. Verifique la contraseña.');
        }

        $certPem = $certs['cert'];
        $pkeyPem = $certs['pkey'];

        // Usar XMLSecurityDSig para firmar
        $doc = new \DOMDocument();
        $doc->loadXML($xml);
        $doc->encoding = 'utf-8';

        $objDSig = new XMLSecurityDSig();
        $objDSig->setCanonicalMethod(XMLSecurityDSig::C14N);
        $objDSig->addReference(
            $doc->documentElement,
            XMLSecurityDSig::SHA256,
            ['http://www.w3.org/2000/09/xmldsig#enveloped-signature', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315']
        );

        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, ['type' => 'private']);
        $objKey->loadKey($pkeyPem, false, true);

        $objDSig->sign($objKey, $doc->documentElement);

        // Agregar certificado público
        $objDSig->add509Cert($certPem);

        return $doc->saveXML();
    }

    /**
     * Genera hash CPE (Code) usado en QR.
     */
    public function generarHashCpe(string $xmlFirmado): string
    {
        return hash('sha256', $xmlFirmado);
    }

    /**
     * Envía el comprobante firmado a SUNAT (vía OSE o GRE).
     */
    public function enviarASunat(Comprobante $comprobante): void
    {
        $venta = $comprobante->venta;

        try {
            $comprobante->increment('intentos_envio');

            if ($this->config->modo_envio === 'ose') {
                $respuesta = $this->enviarPorOse($comprobante);
            } else {
                $respuesta = $this->enviarPorGre($comprobante);
            }

            $this->procesarRespuestaSunat($comprobante, $respuesta);
        } catch (\Exception $e) {
            $comprobante->update([
                'estado' => Comprobante::ESTADO_EXCEPCION,
                'descripcion_respuesta' => $e->getMessage(),
            ]);
            $venta->update(['estado_sunat' => 'excepcion']);
            throw $e;
        }
    }

    /**
     * Envía por OSE (Operador de Servicios Electrónicos) usando REST.
     */
    private function enviarPorOse(Comprobante $comprobante): array
    {
        $url = rtrim($this->config->ose_url, '/') . '/v1/invoice';

        $client = new \GuzzleHttp\Client([
            'timeout' => $this->config->timeout_segundos ?? 30,
        ]);

        $payload = [
            'ruc' => $this->empresa->ruc,
            'tipo_comprobante' => $comprobante->tipo_comprobante,
            'serie' => $comprobante->serie,
            'correlativo' => $comprobante->correlativo,
            'xml' => base64_encode($comprobante->xml_firmado),
            'usuario_sol' => $this->config->usuario_sol,
            'clave_sol' => $this->config->clave_sol,
        ];

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        if ($this->config->token_ose && $this->config->token_ose_vence > now()) {
            $headers['Authorization'] = 'Bearer ' . $this->config->token_ose;
        }

        $start = microtime(true);
        $response = $client->post($url, [
            'json' => $payload,
            'headers' => $headers,
        ]);
        $duration = (int) ((microtime(true) - $start) * 1000);

        $body = $response->getBody()->getContents();
        $data = json_decode($body, true) ?? [];

        SunatRespuesta::create([
            'comprobante_id' => $comprobante->id,
            'tipo_operacion' => 'envio_ose',
            'endpoint' => $url,
            'request_payload' => $payload,
            'response_payload' => $data,
            'http_status' => $response->getStatusCode(),
            'codigo_respuesta' => $data['codigo'] ?? null,
            'descripcion' => $data['descripcion'] ?? null,
            'exito' => $response->getStatusCode() === 200,
            'duracion_ms' => $duration,
        ]);

        return $data;
    }

    /**
     * Envía por GRE (Guía de Remisión Electrónica) usando SOAP.
     */
    private function enviarPorGre(Comprobante $comprobante): array
    {
        $url = $this->config->gre_url;

        $soapRequest = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe">
  <soapenv:Header>
    <wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
      <wsse:UsernameToken>
        <wsse:Username>{$this->config->usuario_sol}</wsse:Username>
        <wsse:Password>{$this->config->clave_sol}</wsse:Password>
      </wsse:UsernameToken>
    </wsse:Security>
  </soapenv:Header>
  <soapenv:Body>
    <ser:sendBill>
      <fileName>{$comprobante->correlativo_completo}.xml</fileName>
      <contentFile>{$this->fileGetContentsBase64($comprobante->xml_firmado)}</contentFile>
    </ser:sendBill>
  </soapenv:Body>
</soapenv:Envelope>
XML;

        $start = microtime(true);
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $soapRequest,
            CURLOPT_HTTPHEADER => [
                'Content-Type: text/xml; charset=utf-8',
                'SOAPAction: "sendBill"',
                'Content-Length: ' . strlen($soapRequest),
            ],
            CURLOPT_TIMEOUT => $this->config->timeout_segundos ?? 30,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        $duration = (int) ((microtime(true) - $start) * 1000);

        if ($error) {
            throw new \RuntimeException("Error CURL: {$error}");
        }

        // Parsear respuesta SOAP
        $data = $this->parsearRespuestaGre($response);

        SunatRespuesta::create([
            'comprobante_id' => $comprobante->id,
            'tipo_operacion' => 'envio_gre',
            'endpoint' => $url,
            'request_payload' => ['xml_size' => strlen($soapRequest)],
            'response_payload' => $data,
            'http_status' => $httpCode,
            'codigo_respuesta' => $data['codigo'] ?? null,
            'descripcion' => $data['descripcion'] ?? null,
            'exito' => isset($data['exito']) && $data['exito'],
            'duracion_ms' => $duration,
        ]);

        return $data;
    }

    private function fileGetContentsBase64(string $xml): string
    {
        return base64_encode($xml);
    }

    private function parsearRespuestaGre(string $response): array
    {
        $data = [
            'codigo' => null,
            'descripcion' => null,
            'exito' => false,
            'ticket' => null,
            'cdr' => null,
        ];

        // Buscar nodo applicationResponse que contiene el CDR (ZIP base64)
        if (preg_match('/<applicationResponse>(.*?)<\/applicationResponse>/s', $response, $matches)) {
            $cdrBase64 = trim($matches[1]);
            $cdrZip = base64_decode($cdrBase64);
            if ($cdrZip !== false) {
                $data['cdr'] = $cdrZip;
                // Extraer XML del CDR
                $cdrXml = $this->extraerXmlDeZip($cdrZip);
                if ($cdrXml) {
                    $codigoRespuesta = $this->extraerCodigoRespuesta($cdrXml);
                    $data['codigo'] = $codigoRespuesta['codigo'] ?? null;
                    $data['descripcion'] = $codigoRespuesta['descripcion'] ?? null;
                    $data['exito'] = ($codigoRespuesta['codigo'] ?? '') === '0';
                }
            }
        }

        // Buscar fault/error
        if (preg_match('/<faultcode>(.*?)<\/faultcode>/s', $response, $matches)) {
            $data['codigo'] = trim(strip_tags($matches[1]));
            $data['exito'] = false;
        }
        if (preg_match('/<faultstring>(.*?)<\/faultstring>/s', $response, $matches)) {
            $data['descripcion'] = trim(strip_tags($matches[1]));
        }

        return $data;
    }

    private function extraerXmlDeZip(string $zipContent): ?string
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'cdr_');
        file_put_contents($tmpFile, $zipContent);

        $zip = new \ZipArchive();
        if ($zip->open($tmpFile) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (str_ends_with($name, '.xml')) {
                    $content = $zip->getFromIndex($i);
                    $zip->close();
                    @unlink($tmpFile);
                    return $content;
                }
            }
            $zip->close();
        }
        @unlink($tmpFile);
        return null;
    }

    private function extraerCodigoRespuesta(string $xml): array
    {
        $data = ['codigo' => null, 'descripcion' => null];

        if (preg_match('/<cbc:ResponseCode>(.*?)<\/cbc:ResponseCode>/', $xml, $m)) {
            $data['codigo'] = trim($m[1]);
        }
        if (preg_match('/<cbc:Description>(.*?)<\/cbc:Description>/', $xml, $m)) {
            $data['descripcion'] = trim($m[1]);
        }

        return $data;
    }

    /**
     * Procesa la respuesta de SUNAT.
     */
    private function procesarRespuestaSunat(Comprobante $comprobante, array $respuesta): void
    {
        $codigo = $respuesta['codigo'] ?? null;
        $descripcion = $respuesta['descripcion'] ?? '';
        $venta = $comprobante->venta;

        // Códigos de éxito: 0 = aceptado, 0000, etc.
        if ($codigo === '0' || $codigo === '0000' || ($respuesta['exito'] ?? false)) {
            $comprobante->update([
                'estado' => Comprobante::ESTADO_ACEPTADO,
                'codigo_respuesta' => $codigo,
                'descripcion_respuesta' => $descripcion ?: 'Aceptado por SUNAT',
                'cdr_path' => isset($respuesta['cdr']) ? $this->guardarCdr($comprobante, $respuesta['cdr']) : null,
                'hash_cdr' => isset($respuesta['cdr']) ? hash('sha256', $respuesta['cdr']) : null,
                'fecha_respuesta' => now(),
            ]);

            $venta->update([
                'estado_sunat' => 'aceptado',
                'estado' => 'aceptado',
            ]);
        } else {
            $estado = in_array($codigo, ['0001', '0002', '0003']) ? Comprobante::ESTADO_EXCEPCION : Comprobante::ESTADO_RECHAZADO;
            $estadoVenta = $estado === Comprobante::ESTADO_EXCEPCION ? 'excepcion' : 'rechazado';

            $comprobante->update([
                'estado' => $estado,
                'codigo_respuesta' => $codigo,
                'descripcion_respuesta' => $descripcion,
                'fecha_respuesta' => now(),
            ]);

            $venta->update(['estado_sunat' => $estadoVenta]);
        }
    }

    private function guardarCdr(Comprobante $comprobante, string $cdrZip): string
    {
        $path = "sunat/cdr/{$this->empresa->ruc}/{$comprobante->correlativo_completo}.zip";
        \Storage::disk('local')->put($path, $cdrZip);
        return $path;
    }

    /**
     * Reenviar un comprobante (re-intentar envío).
     */
    public function reenviar(Comprobante $comprobante): void
    {
        if ($comprobante->intentos_envio >= ($this->config->intentos_max ?? 3)) {
            throw new \RuntimeException('Máximo de intentos alcanzado');
        }
        $this->enviarASunat($comprobante);
    }

    /**
     * Comunicación de baja (anulación) - generar XML y enviar.
     */
    public function comunicacionBaja(Venta $venta): void
    {
        // Generar XML de comunicación de baja
        $correlativo = \App\Models\Serie::siguienteCorrelativo('RA', now()->format('Ymd'));

        $xml = (new XmlGenerator($venta))->generarComunicacionBaja($correlativo['correlativo_completo']);

        // Firmar
        $xmlFirmado = $this->firmarXml($xml);

        // Crear comprobante de baja
        $comprobante = Comprobante::create([
            'venta_id' => $venta->id,
            'tipo_comprobante' => 'RA',
            'serie' => $correlativo['serie'],
            'correlativo' => $correlativo['correlativo'],
            'correlativo_completo' => $correlativo['correlativo_completo'],
            'xml_firmado' => $xmlFirmado,
            'hash_cpe' => $this->generarHashCpe($xmlFirmado),
            'estado' => Comprobante::ESTADO_FIRMADO,
            'fecha_firma' => now(),
        ]);

        // Enviar
        $this->enviarASunat($comprobante);
    }

    /**
     * Generar resumen diario (boletas de venta del día).
     */
    public function resumenDiario(string $fecha): array
    {
        $ventas = Venta::where('tipo_comprobante', '03')
            ->whereDate('fecha_emision', $fecha)
            ->where('estado', '!=', 'anulada')
            ->get();

        if ($ventas->isEmpty()) {
            return ['success' => false, 'message' => 'No hay boletas para este día'];
        }

        $correlativo = \App\Models\Serie::siguienteCorrelativo('RC', now()->format('Ymd'));
        $xml = (new XmlGenerator())->generarResumenDiario($ventas, $correlativo['correlativo_completo'], $fecha);

        $xmlFirmado = $this->firmarXml($xml);

        $ticket = null;
        // Enviar vía GRE/OSE y obtener ticket
        // ...

        return [
            'success' => true,
            'cantidad' => $ventas->count(),
            'total' => $ventas->sum('total'),
            'correlativo' => $correlativo['correlativo_completo'],
            'ticket' => $ticket,
        ];
    }

    /**
     * Consulta el estado de un ticket.
     */
    public function consultarTicket(string $ticket): array
    {
        // ... implementar consulta de ticket
        return ['estado' => 'pendiente'];
    }
}
