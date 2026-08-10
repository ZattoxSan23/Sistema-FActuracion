<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Flujo de Caja</title>
<style>
@page { margin: 1.2cm; size: A4; }
body { font-family: Arial, sans-serif; font-size: 10pt; color: #1e293b; }
.header { display: flex; justify-content: space-between; border-bottom: 3px solid #2563eb; padding-bottom: 10px; margin-bottom: 14px; }
.company-info h1 { margin: 0; color: #2563eb; font-size: 16pt; }
.title { text-align: right; }
.title h2 { margin: 0; color: #2563eb; font-size: 14pt; }
.kpis { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin: 12px 0; }
.kpi { padding: 10px; border-radius: 4px; text-align: center; }
.kpi-blue { background: #dbeafe; }
.kpi-green { background: #d1fae5; }
.kpi-red { background: #fee2e2; }
.kpi h4 { margin: 0; font-size: 9pt; text-transform: uppercase; }
.kpi p { margin: 4px 0 0 0; font-size: 14pt; font-weight: bold; }
table { width: 100%; border-collapse: collapse; margin-top: 12px; }
th { background: #2563eb; color: white; padding: 6px; text-align: left; font-size: 9pt; }
td { padding: 5px 6px; border-bottom: 1px solid #e2e8f0; font-size: 9pt; }
.text-right { text-align: right; }
.footer { margin-top: 14px; font-size: 8pt; color: #64748b; text-align: center; }
</style>
</head>
<body>
<div class="header">
    <div class="company-info">
        <h1>{{ $empresa->nombre_comercial ?? $empresa->razon_social }}</h1>
        <p>RUC: {{ $empresa->ruc }}</p>
    </div>
    <div class="title">
        <h2>Flujo de Caja</h2>
        <p>{{ $filtros['desde'] }} - {{ $filtros['hasta'] }}</p>
        <p>Generado: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</div>

<div class="kpis">
    <div class="kpi kpi-green">
        <h4>Ingresos</h4>
        <p>S/ {{ number_format($totales['ingresos'], 2) }}</p>
    </div>
    <div class="kpi kpi-red">
        <h4>Egresos</h4>
        <p>S/ {{ number_format($totales['egresos'], 2) }}</p>
    </div>
    <div class="kpi kpi-blue">
        <h4>Flujo Neto</h4>
        <p>S/ {{ number_format($totales['neto'], 2) }}</p>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th class="text-right">Ingresos</th>
            <th class="text-right">Egresos</th>
            <th class="text-right">Neto</th>
        </tr>
    </thead>
    <tbody>
        @foreach($datos as $d)
            <tr>
                <td>{{ \Carbon\Carbon::parse($d['fecha'])->format('d/m/Y') }}</td>
                <td class="text-right">S/ {{ number_format($d['ingresos'], 2) }}</td>
                <td class="text-right">S/ {{ number_format($d['egresos'], 2) }}</td>
                <td class="text-right"><strong>S/ {{ number_format($d['neto'], 2) }}</strong></td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    Reporte generado por {{ config('app.name') }} | {{ now()->format('d/m/Y H:i:s') }}
</div>
</body>
</html>
