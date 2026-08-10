@extends('layouts.app')

@section('title', 'Reporte IGV')
@section('header', 'Reporte de IGV')

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
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Resumen Tributario</div>
    <div class="card-body">
        <table class="table">
            <tr>
                <td><strong>Base Imponible (Op. Gravadas)</strong></td>
                <td class="text-end fs-5">S/ {{ number_format($igv['base_imponible'], 2) }}</td>
            </tr>
            <tr class="table-light">
                <td><strong>IGV (18%)</strong></td>
                <td class="text-end fs-4 fw-bold text-primary">S/ {{ number_format($igv['igv'], 2) }}</td>
            </tr>
            <tr>
                <td><strong>Op. Exoneradas</strong></td>
                <td class="text-end">S/ {{ number_format($igv['exoneradas'], 2) }}</td>
            </tr>
            <tr>
                <td><strong>Op. Inafectas</strong></td>
                <td class="text-end">S/ {{ number_format($igv['inafectas'], 2) }}</td>
            </tr>
            <tr class="table-success">
                <td><strong>TOTAL VENTAS</strong></td>
                <td class="text-end fs-4 fw-bold text-success">S/ {{ number_format($igv['total'], 2) }}</td>
            </tr>
        </table>
    </div>
</div>
@endsection
