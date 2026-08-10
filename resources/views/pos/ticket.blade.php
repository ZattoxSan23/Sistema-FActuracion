<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ticket {{ $venta->correlativo }}</title>
    <style>
        @page { margin: 0; size: 80mm auto; }
        body {
            font-family: 'Courier New', monospace;
            width: 80mm;
            margin: 0 auto;
            padding: 4mm;
            font-size: 11pt;
            line-height: 1.3;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        hr { border: none; border-top: 1px dashed #000; margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; }
        table td { padding: 1px 0; vertical-align: top; }
        .item-row td { font-size: 10pt; }
        .total-row td { font-size: 12pt; font-weight: bold; }
        .qr { text-align: center; margin: 8px 0; }
        .qr img { width: 100px; height: 100px; }
        @media print {
            body { width: 80mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="text-align:center; margin-bottom:10px;">
        <button onclick="window.print()">Imprimir</button>
        <button onclick="window.close()">Cerrar</button>
    </div>

    <div class="center bold" style="font-size: 13pt;">{{ $empresa->nombre_comercial ?? $empresa->razon_social }}</div>
    <div class="center">{{ $empresa->razon_social }}</div>
    <div class="center">RUC: {{ $empresa->ruc }}</div>
    <div class="center">{{ $empresa->direccion }}</div>
    @if($empresa->telefono)<div class="center">Tel: {{ $empresa->telefono }}</div>@endif

    <hr>

    <table>
        <tr><td colspan="2" class="center bold">{{ $venta->tipo_comprobante_label }} ELECTRÓNICA</td></tr>
        <tr><td colspan="2" class="center bold" style="font-size: 13pt;">{{ $venta->correlativo }}</td></tr>
    </table>

    <hr>

    <table>
        <tr><td>Fecha:</td><td class="right">{{ $venta->fecha_emision->format('d/m/Y H:i:s') }}</td></tr>
        <tr><td>Cliente:</td><td class="right">{{ $venta->cliente?->nombre_razon_social ?? 'VARIOS' }}</td></tr>
        <tr><td>Doc:</td><td class="right">{{ $venta->cliente?->documento_completo ?? 'SIN DOC.' }}</td></tr>
        @if($venta->cliente?->direccion)
        <tr><td>Dir:</td><td class="right">{{ $venta->cliente->direccion }}</td></tr>
        @endif
        <tr><td>Vendedor:</td><td class="right">{{ $venta->usuario->name }}</td></tr>
    </table>

    <hr>

    <table>
        <thead>
            <tr class="bold">
                <td>Cant.</td>
                <td>Descripción</td>
                <td class="right">P.U.</td>
                <td class="right">Total</td>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->items as $item)
            <tr class="item-row">
                <td>{{ number_format($item->cantidad, 0) }}</td>
                <td>{{ $item->descripcion }}</td>
                <td class="right">{{ number_format($item->precio_unitario_con_igv, 2) }}</td>
                <td class="right">{{ number_format($item->total_item, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <hr>

    <table>
        <tr><td>Op. Gravadas:</td><td class="right">S/ {{ number_format($venta->op_gravadas, 2) }}</td></tr>
        <tr><td>Op. Exoneradas:</td><td class="right">S/ {{ number_format($venta->op_exoneradas, 2) }}</td></tr>
        <tr><td>Op. Inafectas:</td><td class="right">S/ {{ number_format($venta->op_inafectas, 2) }}</td></tr>
        <tr><td>IGV (18%):</td><td class="right">S/ {{ number_format($venta->igv, 2) }}</td></tr>
        <tr class="total-row">
            <td>TOTAL:</td>
            <td class="right">S/ {{ number_format($venta->total, 2) }}</td>
        </tr>
    </table>

    <hr>

    <div class="bold">PAGOS:</div>
    @foreach($venta->pagos as $pago)
    <table>
        <tr><td>{{ $pago->metodo_label }}:</td><td class="right">S/ {{ number_format($pago->monto, 2) }}</td></tr>
        @if($pago->vuelto > 0)
        <tr><td>Vuelto:</td><td class="right">S/ {{ number_format($pago->vuelto, 2) }}</td></tr>
        @endif
        @if($pago->numero_operacion)
        <tr><td>Op:</td><td class="right">{{ $pago->numero_operacion }}</td></tr>
        @endif
    </table>
    @endforeach

    @if($venta->comprobante && $venta->comprobante->hash_cpe)
    <hr>
    <div class="qr">
        <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($venta->comprobante->hash_cpe, 'C128') }}" alt="barcode">
        <div style="font-size: 8pt; word-break: break-all;">{{ $venta->comprobante->hash_cpe }}</div>
    </div>
    @endif

    @if($empresa->mensaje_personalizado)
    <div class="center" style="margin-top: 6px; font-size: 9pt;">{{ $empresa->mensaje_personalizado }}</div>
    @endif
    @if($empresa->pie_pagina_ticket)
    <div class="center" style="margin-top: 6px; font-size: 9pt;">{{ $empresa->pie_pagina_ticket }}</div>
    @endif

    <div class="center" style="margin-top: 8px; font-size: 8pt;">
        Representación impresa de la {{ $venta->tipo_comprobante_label }} Electrónica
        @if($venta->comprobante && $venta->comprobante->isAceptado())
            <br>Autorizado mediante Resolución de Superintendencia
        @endif
    </div>

    <div class="center" style="margin-top: 4px; font-size: 8pt;">
        <strong>¡Gracias por su compra!</strong>
    </div>
</body>
</html>
