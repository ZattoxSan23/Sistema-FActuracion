<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Caja #{{ $caja->id }}</title>
    <style>
        @page { margin: 1cm; size: A4; }
        body { font-family: Arial, sans-serif; font-size: 10pt; color: #333; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
        .company h1 { margin: 0; color: #2563eb; font-size: 16pt; }
        .title { text-align: right; }
        .title h2 { margin: 0; font-size: 14pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        table th { background: #f1f5f9; text-align: left; padding: 6px; border-bottom: 2px solid #cbd5e1; font-size: 9pt; }
        table td { padding: 5px 6px; border-bottom: 1px solid #e2e8f0; font-size: 9pt; }
        .info-table td { padding: 3px 8px; }
        .totales-box { background: #f8fafc; padding: 10px; margin-top: 12px; border-left: 4px solid #2563eb; }
        .totales-box table td { padding: 4px 6px; }
        .text-right { text-align: right; }
        .footer { margin-top: 30px; font-size: 8pt; color: #666; text-align: center; }
        .grand-total { font-size: 14pt; font-weight: bold; color: #2563eb; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company">
            <h1>{{ $empresa->nombre_comercial ?? $empresa->razon_social }}</h1>
            <div>{{ $empresa->ruc }} | {{ $empresa->direccion }}</div>
        </div>
        <div class="title">
            <h2>REPORTE DE CAJA</h2>
            <div><strong>N° {{ str_pad($caja->id, 6, '0', STR_PAD_LEFT) }}</strong></div>
            <div>{{ $caja->fecha_apertura->format('d/m/Y') }}</div>
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Cajero:</strong></td>
            <td>{{ $caja->usuarioApertura->name }}</td>
            <td><strong>Estado:</strong></td>
            <td>{{ ucfirst($caja->estado) }}</td>
        </tr>
        <tr>
            <td><strong>Apertura:</strong></td>
            <td>{{ $caja->fecha_apertura->format('d/m/Y H:i:s') }}</td>
            <td><strong>Cierre:</strong></td>
            <td>{{ $caja->fecha_cierre?->format('d/m/Y H:i:s') ?? '—' }}</td>
        </tr>
        @if($caja->observaciones_apertura)
        <tr>
            <td><strong>Obs. Apertura:</strong></td>
            <td colspan="3">{{ $caja->observaciones_apertura }}</td>
        </tr>
        @endif
        @if($caja->observaciones_cierre)
        <tr>
            <td><strong>Obs. Cierre:</strong></td>
            <td colspan="3">{{ $caja->observaciones_cierre }}</td>
        </tr>
        @endif
    </table>

    <h3 style="margin-top: 14px; color: #2563eb; font-size: 11pt;">Resumen Financiero</h3>
    <div class="totales-box">
        <table>
            <tr><td>Monto Apertura:</td><td class="text-right">S/ {{ number_format($caja->monto_apertura, 2) }}</td></tr>
            <tr><td>Ventas en Efectivo:</td><td class="text-right">+ S/ {{ number_format($caja->total_ventas_efectivo, 2) }}</td></tr>
            <tr><td>Ventas con Tarjeta:</td><td class="text-right">S/ {{ number_format($caja->total_ventas_tarjeta, 2) }}</td></tr>
            <tr><td>Ventas Yape/Plin:</td><td class="text-right">S/ {{ number_format($caja->total_ventas_yape, 2) }}</td></tr>
            <tr><td>Ventas Transferencia:</td><td class="text-right">S/ {{ number_format($caja->total_ventas_transferencia, 2) }}</td></tr>
            <tr><td>Ingresos Manuales:</td><td class="text-right">+ S/ {{ number_format($caja->total_ingresos, 2) }}</td></tr>
            <tr><td>Egresos Manuales:</td><td class="text-right">- S/ {{ number_format($caja->total_egresos, 2) }}</td></tr>
            <tr style="border-top: 2px solid #2563eb;">
                <td class="grand-total">Efectivo Teórico:</td>
                <td class="text-right grand-total">S/ {{ number_format($caja->monto_efectivo_teorico, 2) }}</td>
            </tr>
            @if($caja->estado === 'cerrada')
            <tr><td>Efectivo Real (conteo):</td><td class="text-right">S/ {{ number_format($caja->monto_efectivo_real, 2) }}</td></tr>
            <tr>
                <td><strong>Diferencia:</strong></td>
                <td class="text-right" style="color: {{ $caja->diferencia == 0 ? 'green' : ($caja->diferencia > 0 ? 'green' : 'red') }};">
                    <strong>S/ {{ number_format($caja->diferencia, 2) }}</strong>
                </td>
            </tr>
            @endif
        </table>
    </div>

    <h3 style="margin-top: 18px; color: #2563eb; font-size: 11pt;">Detalle de Movimientos</h3>
    <table>
        <thead>
            <tr>
                <th>Hora</th>
                <th>Tipo</th>
                <th>Concepto</th>
                <th>Método</th>
                <th class="text-right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($caja->movimientos as $mov)
                <tr>
                    <td>{{ $mov->fecha->format('H:i') }}</td>
                    <td>{{ $mov->tipo_label }}</td>
                    <td>{{ $mov->concepto }}</td>
                    <td>{{ ucfirst($mov->metodo_pago) }}</td>
                    <td class="text-right">S/ {{ number_format($mov->monto, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3 style="margin-top: 18px; color: #2563eb; font-size: 11pt;">Ventas de la Caja ({{ $caja->cantidad_ventas }})</h3>
    <table>
        <thead>
            <tr>
                <th>Hora</th>
                <th>Comprobante</th>
                <th>Cliente</th>
                <th>Método de Pago</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($caja->ventas as $venta)
                <tr>
                    <td>{{ $venta->fecha_emision->format('H:i') }}</td>
                    <td>{{ $venta->correlativo }}</td>
                    <td>{{ $venta->cliente?->nombre_razon_social ?? 'VARIOS' }}</td>
                    <td>
                        @foreach($venta->pagos as $pago)
                            {{ $pago->metodo_label }}{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                    </td>
                    <td class="text-right">S/ {{ number_format($venta->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Reporte generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>{{ $empresa->razon_social }} - RUC: {{ $empresa->ruc }}</p>
    </div>
</body>
</html>
