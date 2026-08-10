@extends('layouts.app')

@section('title', 'Comprobantes SUNAT')
@section('header', 'Comprobantes Electrónicos')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="estado" class="form-select form-select-sm">
                    <option value="">Todos los estados</option>
                    <option value="aceptado" {{ request('estado') == 'aceptado' ? 'selected' : '' }}>Aceptado</option>
                    <option value="rechazado" {{ request('estado') == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                    <option value="excepcion" {{ request('estado') == 'excepcion' ? 'selected' : '' }}>Excepción</option>
                    <option value="firmado" {{ request('estado') == 'firmado' ? 'selected' : '' }}>Firmado</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="tipo" class="form-select form-select-sm">
                    <option value="">Todos los tipos</option>
                    <option value="03" {{ request('tipo') == '03' ? 'selected' : '' }}>Boletas</option>
                    <option value="01" {{ request('tipo') == '01' ? 'selected' : '' }}>Facturas</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="desde" class="form-control form-control-sm" value="{{ request('desde') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="hasta" class="form-control form-control-sm" value="{{ request('hasta') }}">
            </div>
            <div class="col-12">
                <button class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i>Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th>
                    <th>Comprobante</th>
                    <th>Cliente</th>
                    <th>Estado</th>
                    <th>Código</th>
                    <th>Intentos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($comprobantes as $comp)
                    <tr>
                        <td>{{ $comp->fecha_emision->format('d/m/Y H:i') }}</td>
                        <td><code>{{ $comp->correlativo_completo }}</code></td>
                        <td>{{ $comp->venta->cliente?->nombre_razon_social ?? '—' }}</td>
                        <td>
                            @php
                                $estados = [
                                    'borrador' => ['secondary', 'Borrador'],
                                    'firmado' => ['info', 'Firmado'],
                                    'enviado' => ['primary', 'Enviado'],
                                    'aceptado' => ['success', 'Aceptado'],
                                    'rechazado' => ['danger', 'Rechazado'],
                                    'excepcion' => ['warning', 'Excepción'],
                                ];
                                $est = $estados[$comp->estado] ?? ['secondary', $comp->estado];
                            @endphp
                            <span class="badge bg-{{ $est[0] }}-subtle text-{{ $est[0] }}">{{ $est[1] }}</span>
                        </td>
                        <td><code>{{ $comp->codigo_respuesta ?? '—' }}</code></td>
                        <td>{{ $comp->intentos_envio }}</td>
                        <td>
                            <a href="{{ route('sunat.comprobante', $comp) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if(in_array($comp->estado, ['rechazado', 'excepcion', 'firmado']))
                                <form action="{{ route('sunat.reenviar', $comp) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-redo"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Sin comprobantes</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($comprobantes->hasPages())
        <div class="card-footer">{{ $comprobantes->links() }}</div>
    @endif
</div>
@endsection
