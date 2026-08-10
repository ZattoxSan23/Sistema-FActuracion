<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Estado de Cuenta - {{ $cliente->nombre_razon_social }}</title>
<style>
@page { margin: 1.2cm; size: A4; }
body { font-family: Arial, sans-serif; font-size: 10pt; }
.header { display: flex; justify-content: space-between; border-bottom: 3px solid #2563eb; padding-bottom: 10px; margin-bottom: 14px; }
.company-info h1 { margin: 0; color: #2563eb; font-size: 16pt; }
.title { text-align: right; }
.title h2 { margin: 0; color: #2563eb; font-size: 14pt; }
.cliente-info { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 12px; }
.cliente-box { padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; }
table { width: 100%; border-collapse: collapse; margin-top: 8px; }
th { background: #2563eb; color: white; padding: 6px; text-align: left; font-size: 9pt; }
td { padding: 5px 6px; border-bottom: 1px solid #e2e8f0; font-size: 9pt; }
.text-right { text-align: right; }
.totales { background: #f1f5f9; padding: 8px; border-radius: 4px; margin: 10px 0; }
.totales p { margin: 2px 0; font-size: 10pt; }
</style>
</head>
<body>
<div class="header">
    <div class="company-info">
        <h1>{{ $empresa->nombre_comercial ?? $empresa->razon_social }}</h1>
        <p>RUC: {{ $empresa->ruc }}</p>
    </div>
    <div class="title">
        <h2>Estado de Cuenta</h2>
        <p>{{ $filtros['desde'] }} - {{ $filtros['hasta'] }}</p>
    </div>
</div>

<div class="cliente-info">
    <div class="cliente-box">
        <p><strong>Cliente:</strong> {{ $cliente->nombre_razon_social }}</p>
        <p><strong>Documento:</strong> {{ $cliente->tipo_documento }}: {{ $cliente->numero_documento }}</p>
    </div>
    <div class="cliente-box">
        @if($cliente->direccion)<p><strong>Dirección:</strong> {{ $cliente->direccion }}</p>@endif
        @if($cliente->telefono)<p><strong>Teléfono:</strong> {{ $cliente->telefono }}</p>@endif
        @if($cliente->email)<p><strong>Email:</strong> {{ $cliente->email }}</p>@endif
    </div>
</div>

<div class="totales">
    <p><strong>Total Compras:</strong> S/ {{ number_format($totales['total'], 2) }}</p>
    <p><strong>Cantidad:</strong> {{ $totales['cantidad'] }} ({{ $totales['boletas'] }} boletas, {{ $totales['facturas'] }} facturas)</p>
    <p><strong>IGV:</strong> S/ {{ number_format($totales['igv'], 2) }}</p>
</div>

<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Comprobante</th>
            <th>Tipo</th>
            <th>Estado</th>
            <th class="text-right">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ventas as $v)
            <tr>
                <td>{{ $v->fecha_emision->format('d/m/Y H:i') }}</td>
                <td>{{ $v->correlativo }}</td>
                <td>{{ $v->tipo_comprobante_label }}</td>
                <td>{{ $v->comprobante?->estado ?? '—' }}</td>
                <td class="text-right">S/ {{ number_format($v->total, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
