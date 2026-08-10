<?php

namespace App\Services\Printer;

use App\Models\Empresa;
use App\Models\Venta;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\EscposImage;

/**
 * Servicio de impresión multi-formato:
 * - Ticket 80mm (ESC/POS para impresoras térmicas)
 * - A4 / A5 (PDF con DomPDF)
 */
class PrinterService
{
    /**
     * Imprime un comprobante en formato ticket 80mm usando ESC/POS.
     */
    public function imprimirTicket(Venta $venta): bool
    {
        try {
            $empresa = Empresa::actual();

            // Determinar conector según sistema operativo
            $connector = $this->getConnector();
            if (!$connector) {
                throw new \RuntimeException('No se pudo conectar con la impresora');
            }

            $printer = new Printer($connector);

            // Encabezado
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setTextSize(2, 2);
            $printer->text($empresa->nombre_comercial ?? $empresa->razon_social . "\n");
            $printer->setTextSize(1, 1);
            $printer->text($empresa->razon_social . "\n");
            $printer->text("RUC: {$empresa->ruc}\n");
            $printer->text($empresa->direccion . "\n");
            $printer->text("Tel: {$empresa->telefono}\n");
            $printer->text("--------------------------------\n");

            // Comprobante
            $printer->setTextSize(1, 1);
            $printer->text($venta->tipo_comprobante_label . " ELECTRONICA\n");
            $printer->setTextSize(2, 2);
            $printer->text($venta->correlativo . "\n");
            $printer->setTextSize(1, 1);
            $printer->text("--------------------------------\n");

            // Datos del cliente
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Fecha: {$venta->fecha_emision->format('d/m/Y H:i')}\n");
            $printer->text("Cliente: " . ($venta->cliente?->nombre_razon_social ?? 'VARIOS') . "\n");
            $printer->text("Doc: " . ($venta->cliente?->documento_completo ?? '—') . "\n");

            $printer->text("--------------------------------\n");

            // Items
            foreach ($venta->items as $item) {
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text("{$item->cantidad} x {$item->descripcion}\n");
                $printer->setJustification(Printer::JUSTIFY_RIGHT);
                $printer->text("S/ " . number_format($item->precio_unitario_con_igv, 2) . "  S/ " . number_format($item->total_item, 2) . "\n");
            }

            $printer->text("--------------------------------\n");

            // Totales
            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->text("Op. Gravadas: S/ " . number_format($venta->op_gravadas, 2) . "\n");
            $printer->text("IGV:          S/ " . number_format($venta->igv, 2) . "\n");
            $printer->setTextSize(2, 2);
            $printer->text("TOTAL: S/ " . number_format($venta->total, 2) . "\n");
            $printer->setTextSize(1, 1);
            $printer->text("--------------------------------\n");

            // Pagos
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            foreach ($venta->pagos as $pago) {
                $printer->text("{$pago->metodo_label}: S/ " . number_format($pago->monto, 2) . "\n");
                if ($pago->vuelto > 0) {
                    $printer->text("  Vuelto: S/ " . number_format($pago->vuelto, 2) . "\n");
                }
            }

            // Mensaje
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("--------------------------------\n");
            if ($empresa->mensaje_personalizado) {
                $printer->text($empresa->mensaje_personalizado . "\n");
            }
            $printer->text("¡Gracias por su compra!\n");
            $printer->feed(3);
            $printer->cut();

            $printer->close();

            return true;
        } catch (\Exception $e) {
            \Log::error('Error al imprimir ticket: ' . $e->getMessage());
            return false;
        }
    }

    private function getConnector()
    {
        $os = PHP_OS;
        $printerPath = env('PRINTER_TICKET_PATH', '/dev/usb/lp0');

        if (str_starts_with($os, 'Linux')) {
            if (file_exists($printerPath)) {
                return new FilePrintConnector($printerPath);
            }
        } elseif (str_starts_with($os, 'WIN')) {
            return new WindowsPrintConnector(env('PRINTER_TICKET_NAME', 'Impresora Tickets'));
        } elseif (str_starts_with($os, 'Darwin')) {
            try {
                return new CupsPrintConnector(env('PRINTER_TICKET_NAME', 'Impresora_Tickets'));
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }
}
