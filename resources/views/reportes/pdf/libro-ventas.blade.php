<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Libro de Ventas - {{ $filtros['desde'] }} al {{ $filtros['hasta'] }}</title>
<style>
@page { margin: 1cm; size: A4 landscape; }
body { font-family: Arial, sans-serif; font-size: 9pt; color: #1e293b; }
.header { display: flex; justify-content: space-between; border-bottom: 3px solid #2563eb; padding-bottom: 8px; margin-bottom: 10px; }
.company-info h1 { margin: 0; color: #2563eb; font-size: 14pt; }
.title { text-align: right; }
.title h2 { margin: 0; color: #2563eb; font-size: 12pt; }
table { width: 100%; border-collapse: collapse; }
th { background: #2563eb; color: white; padding: 5px 4px; text-align: left; font-size: 8pt; }
td { padding: 4px; border-bottom: 1px solid #e2e8f0; font-size: 8pt; }
.text-right { text-align: right; }
.footer { margin-top: 10px; font-size: 7pt; color: #64748b; text-align: center; }
</style>
</head>
<body>
<div class="header">
    <div class="company-info">
        <h1>{{ $empresa->nombre_comercial ?? $empresa->razon_social }}</h1>
        <p>RUC: {{ $empresa->ruc }} | {{ $empresa->direccion }}</p>
    </div>
    <div class="title">
        <h2>Libro de Ventas</h2>
        <p>Periodo: {{ $filtros['desde'] }} al {{ $filtros['hasta'] }}</p>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>N°</th>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Serie-Número</th>
            <th>Tipo Doc.</th>
            <th>RUC/DNI</th>
            <th>Cliente</th>
            <th class="text-right">Gravadas</th>
            <th class="text-right">Exoneradas</th>
            <th class="text-right">Inafectas</th>
            <th class="text-right">IGV</th>
            <th class="text-right">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ventas as $i => $v)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $v->fecha_emision->format('d/m/Y') }}</td>
                <td>{{ $v->tipo_comprobante_label }}</td>
                <td>{{ $v->correlativo }}</td>
                <td>{{ $v->cliente?->tipo_documento ?? '—' }}</td>
                <td>{{ $v->cliente?->numero_documento ?? '—' }}</td>
                <td>{{ $v->cliente?->nombre_razon_social ?? '—' }}</td>
                <td class="text-right">{{ number_format($v->op_gravadas, 2) }}</td>
                <td class="text-right">{{ number_format($v->op_exoneradas, 2) }}</td>
                <td class="text-right">{{ number_format($v->op_inafectas, 2) }}</td>
                <td class="text-right">{{ number_format($v->igv, 2) }}</td>
                <td class="text-right"><strong>{{ number_format($v->total, 2) }}</strong></td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#f1f5f9;font-weight:bold;">
            <td colspan="7" class="text-right">TOTALES:</td>
            <td class="text-right">{{ number_format($ventas->sum('op_gravadas'), 2) }}</td>
            <td class="text-right">{{ number_format($ventas->sum('op_exoneradas'), 2) }}</td>
            <td class="text-right">{{ number_format($ventas->sum('op_inafectas'), 2) }}</td>
            <td class="text-right">{{ number_format($ventas->sum('igv'), 2) }}</td>
            <td class="text-right">{{ number_format($ventas->sum('total'), 2) }}</td>
        </tr>
    </tfoot>
</table>

<div class="footer">
    Registro de Ventas Electrónico - {{ config('app.name') }} | {{ now()->format('d/m/Y H:i') }}
</div>
</body>
</html>
