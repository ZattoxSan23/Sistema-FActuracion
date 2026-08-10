<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Resumen Diario - {{ $fecha }}</title>
<style>
@page { margin: 1.2cm; size: A4; }
body { font-family: Arial, sans-serif; font-size: 10pt; }
.header { display: flex; justify-content: space-between; border-bottom: 3px solid #2563eb; padding-bottom: 10px; margin-bottom: 14px; }
.company-info h1 { margin: 0; color: #2563eb; font-size: 16pt; }
.title { text-align: right; }
.title h2 { margin: 0; color: #2563eb; font-size: 14pt; }
.kpis { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin: 12px 0; }
.kpi { padding: 8px; border-radius: 4px; text-align: center; }
.kpi-blue { background: #dbeafe; }
.kpi-green { background: #d1fae5; }
.kpi-red { background: #fee2e2; }
.kpi-yellow { background: #fef3c7; }
.kpi h4 { margin: 0; font-size: 8pt; text-transform: uppercase; }
.kpi p { margin: 4px 0 0 0; font-size: 12pt; font-weight: bold; }
.section { margin-top: 14px; }
.section h3 { color: #2563eb; font-size: 12pt; margin-bottom: 6px; }
table { width: 100%; border-collapse: collapse; }
th { background: #2563eb; color: white; padding: 6px; text-align: left; font-size: 9pt; }
td { padding: 5px 6px; border-bottom: 1px solid #e2e8f0; font-size: 9pt; }
.text-right { text-align: right; }
</style>
</head>
<body>
<div class="header">
    <div class="company-info">
        <h1>{{ $empresa->nombre_comercial ?? $empresa->razon_social }}</h1>
        <p>RUC: {{ $empresa->ruc }}</p>
    </div>
    <div class="title">
        <h2>Resumen Diario</h2>
        <p><strong>{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</strong></p>
    </div>
</div>

<div class="kpis">
    <div class="kpi kpi-blue">
        <h4>Ventas</h4>
        <p>{{ $datos['kpis']['ventas_count'] }}</p>
    </div>
    <div class="kpi kpi-green">
        <h4>Ingresos</h4>
        <p>S/ {{ number_format($datos['kpis']['total_ventas'], 2) }}</p>
    </div>
    <div class="kpi kpi-red">
        <h4>Egresos</h4>
        <p>S/ {{ number_format($datos['kpis']['total_egresos'], 2) }}</p>
    </div>
    <div class="kpi kpi-yellow">
        <h4>Neto</h4>
        <p>S/ {{ number_format($datos['kpis']['neto'], 2) }}</p>
    </div>
</div>

<div class="section">
    <h3>Por Método de Pago</h3>
    <table>
        <thead>
            <tr>
                <th>Método</th>
                <th class="text-right">Cantidad</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($datos['por_metodo'] as $metodo => $info)
                <tr>
                    <td>{{ ucfirst($metodo) }}</td>
                    <td class="text-right">{{ $info['cantidad'] }}</td>
                    <td class="text-right">S/ {{ number_format($info['total'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="section">
    <h3>Top 10 Productos</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th class="text-right">Cantidad</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($datos['top_productos'] as $i => $p)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $p->nombre }}</td>
                    <td class="text-right">{{ number_format($p->cantidad, 2) }}</td>
                    <td class="text-right">S/ {{ number_format($p->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
