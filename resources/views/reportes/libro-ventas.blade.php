@extends('layouts.app')

@section('title', 'Libro de Ventas')
@section('header', 'Libro de Ventas')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small mb-1">Desde</label>
                <input type="date" name="desde" class="form-control form-control-sm" value="{{ $filtros['desde'] }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Hasta</label>
                <input type="date" name="hasta" class="form-control form-control-sm" value="{{ $filtros['hasta'] }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Cajero</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach($usuarios ?? [] as $u)
                        <option value="{{ $u->id }}" {{ ($filtros['user_id'] ?? '') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5 d-flex gap-2 justify-content-end">
                <button class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i>Filtrar</button>
                <a href="{{ route('reportes.libro.ventas.pdf', $filtros) }}" target="_blank" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i>PDF
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>N°</th>
                        <th>Fecha Emisión</th>
                        <th>Tipo</th>
                        <th>Serie-Número</th>
                        <th>Tipo Doc.</th>
                        <th>RUC/DNI</th>
                        <th>Cliente</th>
                        <th class="text-end">Gravadas</th>
                        <th class="text-end">Exoneradas</th>
                        <th class="text-end">Inafectas</th>
                        <th class="text-end">IGV</th>
                        <th class="text-end">Total</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $index => $venta)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $venta->fecha_emision->format('d/m/Y') }}</td>
                            <td>{{ $venta->tipo_comprobante_label }}</td>
                            <td>{{ $venta->correlativo }}</td>
                            <td>{{ $venta->cliente?->tipo_documento ?? '—' }}</td>
                            <td>{{ $venta->cliente?->numero_documento ?? '—' }}</td>
                            <td>{{ $venta->cliente?->nombre_razon_social ?? '—' }}</td>
                            <td class="text-end">{{ number_format($venta->op_gravadas, 2) }}</td>
                            <td class="text-end">{{ number_format($venta->op_exoneradas, 2) }}</td>
                            <td class="text-end">{{ number_format($venta->op_inafectas, 2) }}</td>
                            <td class="text-end">{{ number_format($venta->igv, 2) }}</td>
                            <td class="text-end fw-semibold">{{ number_format($venta->total, 2) }}</td>
                            <td>
                                @if($venta->comprobante && $venta->comprobante->isAceptado())
                                    <span class="badge bg-success">Aceptado</span>
                                @elseif($venta->estado === 'anulada')
                                    <span class="badge bg-dark">Anulada</span>
                                @else
                                    <span class="badge bg-warning">{{ ucfirst($venta->estado_sunat) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="13" class="text-center text-muted py-4">Sin registros</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="7" class="text-end">TOTALES:</th>
                        <th class="text-end">{{ number_format($ventas->sum('op_gravadas'), 2) }}</th>
                        <th class="text-end">{{ number_format($ventas->sum('op_exoneradas'), 2) }}</th>
                        <th class="text-end">{{ number_format($ventas->sum('op_inafectas'), 2) }}</th>
                        <th class="text-end">{{ number_format($ventas->sum('igv'), 2) }}</th>
                        <th class="text-end fw-bold">{{ number_format($ventas->sum('total'), 2) }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
