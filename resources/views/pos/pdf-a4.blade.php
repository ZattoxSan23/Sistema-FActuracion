<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $venta->tipo_comprobante_label }} {{ $venta->correlativo }}</title>
    <style>
        @page { margin: 1cm; size: A4; }
        body { font-family: Arial, sans-serif; font-size: 10pt; color: #333; }
        .container { max-width: 100%; }
        table { width: 100%; border-collapse: collapse; }
        .header { display: flex; justify-content: space-between; border-bottom: 3px solid #2563eb; padding-bottom: 12px; }
        .company-info h1 { margin: 0; color: #2563eb; font-size: 18pt; }
        .company-info p { margin: 2px 0; font-size: 9pt; }
        .document-info { text-align: right; }
        .document-info .tipo { font-size: 11pt; color: #2563eb; font-weight: bold; }
        .document-info .numero { font-size: 16pt; font-weight: bold; border: 2px solid #2563eb; padding: 6px 12px; display: inline-block; margin-top: 4px; }
        .cliente-box { margin: 14px 0; padding: 10px; border: 1px solid #ddd; background: #f9fafb; }
        .cliente-box table td { padding: 2px 6px; }
        .cliente-box strong { color: #1e293b; }
        table.items { margin-top: 10px; }
        table.items th { background: #2563eb; color: white; padding: 8px 6px; text-align: left; font-size: 9pt; }
        table.items td { padding: 6px; border-bottom: 1px solid #eee; font-size: 9pt; }
        .totales { margin-top: 12px; }
        .totales td { padding: 3px 8px; }
        .totales .grand-total { font-size: 13pt; font-weight: bold; background: #2563eb; color: white; }
        .pagos { margin-top: 14px; }
        .footer { margin-top: 30px; padding-top: 14px; border-top: 1px solid #ddd; font-size: 8pt; color: #666; text-align: center; }
        .qr-section { text-align: center; margin-top: 20px; }
        .qr-section img { width: 80px; height: 80px; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-info">
                <h1>{{ $empresa->nombre_comercial ?? $empresa->razon_social }}</h1>
                <p>{{ $empresa->razon_social }}</p>
                <p>RUC: {{ $empresa->ruc }}</p>
                <p>{{ $empresa->direccion }}</p>
                @if($empresa->telefono)<p>Tel: {{ $empresa->telefono }}</p>@endif
                @if($empresa->email)<p>{{ $empresa->email }}</p>@endif
            </div>
            <div class="document-info">
                <div class="tipo">{{ $venta->tipo_comprobante_label }} DE VENTA ELECTRÓNICA</div>
                <div class="numero">{{ $venta->correlativo }}</div>
            </div>
        </div>

        <div class="cliente-box">
            <table>
                <tr>
                    <td><strong>Fecha Emisión:</strong> {{ $venta->fecha_emision->format('d/m/Y H:i:s') }}</td>
                    <td><strong>Moneda:</strong> {{ $venta->moneda }}</td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Señor(es):</strong> {{ $venta->cliente?->nombre_razon_social ?? 'CLIENTES VARIOS' }}</td>
                </tr>
                <tr>
                    <td><strong>{{ $venta->cliente?->tipo_documento ?? 'DOC' }}:</strong> {{ $venta->cliente?->numero_documento ?? '—' }}</td>
                    <td><strong>Dirección:</strong> {{ $venta->cliente?->direccion ?? '—' }}</td>
                </tr>
            </table>
        </div>

        <table class="items">
            <thead>
                <tr>
                    <th style="width:40px;">N°</th>
                    <th style="width:80px;">Código</th>
                    <th>Descripción</th>
                    <th style="width:50px;">Und.</th>
                    <th style="width:60px;" class="text-right">Cantidad</th>
                    <th style="width:80px;" class="text-right">P. Unit</th>
                    <th style="width:80px;" class="text-right">Subtotal</th>
                    <th style="width:60px;" class="text-right">IGV</th>
                    <th style="width:80px;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->items as $item)
                    <tr>
                        <td>{{ $item->orden }}</td>
                        <td>{{ $item->codigo_producto }}</td>
                        <td>{{ $item->descripcion }}</td>
                        <td>{{ $item->unidad_medida }}</td>
                        <td class="text-right">{{ number_format($item->cantidad, 3) }}</td>
                        <td class="text-right">{{ number_format($item->precio_unitario, 4) }}</td>
                        <td class="text-right">{{ number_format($item->subtotal, 2) }}</td>
                        <td class="text-right">{{ number_format($item->igv_item, 2) }}</td>
                        <td class="text-right">{{ number_format($item->total_item, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totales" style="margin-top: 10px;">
            <tr>
                <td colspan="7"></td>
                <td><strong>Op. Gravadas:</strong></td>
                <td class="text-right">S/ {{ number_format($venta->op_gravadas, 2) }}</td>
            </tr>
            <tr>
                <td colspan="7"></td>
                <td><strong>Op. Exoneradas:</strong></td>
                <td class="text-right">S/ {{ number_format($venta->op_exoneradas, 2) }}</td>
            </tr>
            <tr>
                <td colspan="7"></td>
                <td><strong>Op. Inafectas:</strong></td>
                <td class="text-right">S/ {{ number_format($venta->op_inafectas, 2) }}</td>
            </tr>
            <tr>
                <td colspan="7"></td>
                <td><strong>IGV (18%):</strong></td>
                <td class="text-right">S/ {{ number_format($venta->igv, 2) }}</td>
            </tr>
            <tr class="grand-total">
                <td colspan="7"></td>
                <td>TOTAL:</td>
                <td class="text-right">S/ {{ number_format($venta->total, 2) }}</td>
            </tr>
        </table>

        <div class="pagos">
            <h3 style="margin-bottom: 6px; font-size: 11pt;">Información de Pago</h3>
            <table style="width: 50%;">
                @foreach($venta->pagos as $pago)
                    <tr>
                        <td><strong>{{ $pago->metodo_label }}:</strong></td>
                        <td class="text-right">S/ {{ number_format($pago->monto, 2) }}</td>
                    </tr>
                    @if($pago->monto_recibido)
                    <tr>
                        <td>Recibido:</td>
                        <td class="text-right">S/ {{ number_format($pago->monto_recibido, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Vuelto:</td>
                        <td class="text-right">S/ {{ number_format($pago->vuelto, 2) }}</td>
                    </tr>
                    @endif
                    @if($pago->numero_operacion)
                    <tr>
                        <td>N° Operación:</td>
                        <td class="text-right">{{ $pago->numero_operacion }}</td>
                    </tr>
                    @endif
                @endforeach
            </table>
        </div>

        @if($venta->comprobante && $venta->comprobante->hash_cpe)
            <div class="qr-section">
                <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($venta->comprobante->hash_cpe, 'C128') }}" alt="barcode">
                <div style="font-size: 7pt; word-break: break-all; max-width: 250px; margin: 4px auto;">
                    {{ $venta->comprobante->hash_cpe }}
                </div>
            </div>
        @endif

        <div class="footer">
            Representación impresa de la {{ $venta->tipo_comprobante_label }} Electrónica<br>
            @if($venta->comprobante)
                Estado: <strong>{{ $venta->comprobante->estado_label }}</strong>
                @if($venta->comprobante->codigo_respuesta)
                    | Código: {{ $venta->comprobante->codigo_respuesta }}
                @endif
            @endif
            <br>
            @if($empresa->mensaje_personalizado)
                <em>{{ $empresa->mensaje_personalizado }}</em><br>
            @endif
            <strong>¡Gracias por su compra!</strong>
        </div>
    </div>
</body>
</html>
