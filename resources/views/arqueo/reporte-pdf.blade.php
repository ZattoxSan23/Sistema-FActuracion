<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Arqueo de Caja #{{ $caja->id }}</title>
<style>
@page { margin: 1.2cm; size: A4; }
body { font-family: Arial, sans-serif; font-size: 10pt; color: #1e293b; }
.header { display: flex; justify-content: space-between; border-bottom: 3px solid #2563eb; padding-bottom: 10px; margin-bottom: 14px; }
.company-info h1 { margin: 0; color: #2563eb; font-size: 16pt; }
.title { text-align: right; }
.title h2 { margin: 0; color: #2563eb; font-size: 14pt; }
table { width: 100%; border-collapse: collapse; margin: 12px 0; }
th { background: #2563eb; color: white; padding: 6px; text-align: left; font-size: 9pt; }
td { padding: 5px 6px; border-bottom: 1px solid #e2e8f0; font-size: 9pt; }
.text-right { text-align: right; }
.text-center { text-align: center; }
.kpis { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 14px 0; }
.kpi { padding: 10px; border-radius: 4px; text-align: center; }
.kpi-blue { background: #dbeafe; }
.kpi-yellow { background: #fef3c7; }
.kpi-green { background: #d1fae5; }
.kpi-red { background: #fee2e2; }
.kpi h4 { margin: 0; font-size: 9pt; text-transform: uppercase; }
.kpi p { margin: 4px 0 0 0; font-size: 14pt; font-weight: bold; }
.footer { margin-top: 16px; font-size: 8pt; color: #64748b; text-align: center; }
</style>
</head>
<body>
<div class="header">
    <div class="company-info">
        <h1>{{ $empresa->nombre_comercial ?? $empresa->razon_social }}</h1>
        <p>RUC: {{ $empresa->ruc }}</p>
    </div>
    <div class="title">
        <h2>Arqueo de Caja</h2>
        <p>Caja #{{ $caja->id }}</p>
        <p>{{ $caja->fecha_apertura->format('d/m/Y H:i') }}</p>
    </div>
</div>

<div class="kpis">
    <div class="kpi kpi-yellow">
        <h4>Efectivo Teórico (Sistema)</h4>
        <p>S/ {{ number_format($caja->monto_efectivo_teorico, 2) }}</p>
    </div>
    <div class="kpi kpi-blue">
        <h4>Total Contado</h4>
        <p>S/ {{ number_format($totalContado, 2) }}</p>
    </div>
    <div class="kpi kpi-green">
        <h4>Diferencia (positivo)</h4>
        <p>S/ {{ number_format(max(0, $totalContado - $caja->monto_efectivo_teorico), 2) }}</p>
    </div>
    <div class="kpi kpi-red">
        <h4>Diferencia (negativo)</h4>
        <p>S/ {{ number_format(min(0, $totalContado - $caja->monto_efectivo_teorico), 2) }}</p>
    </div>
</div>

<h3>Detalle del Conteo</h3>
<table>
    <thead>
        <tr>
            <th>Denominación</th>
            <th class="text-center">Cantidad</th>
            <th class="text-right">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($detalles as $d)
            <tr>
                <td><strong>S/ {{ number_format($d->denominacion, 2) }}</strong></td>
                <td class="text-center">{{ $d->cantidad }}</td>
                <td class="text-right">S/ {{ number_format($d->subtotal, 2) }}</td>
            </tr>
        @endforeach
        <tr style="background:#f1f5f9;font-weight:bold;">
            <td colspan="2" class="text-right">TOTAL:</td>
            <td class="text-right">S/ {{ number_format($totalContado, 2) }}</td>
        </tr>
    </tbody>
</table>

<div class="footer">
    Arqueo realizado por: {{ auth()->user()->name ?? '—' }} | {{ now()->format('d/m/Y H:i') }}
</div>
</body>
</html>
