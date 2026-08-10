@extends('layouts.app')

@section('title', 'Reporte Mensual')
@section('header', 'Reporte Mensual')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="año" class="form-select form-select-sm">
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ $año == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3"><button class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i>Filtrar</button></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Mes</th>
                    <th class="text-end">Cantidad</th>
                    <th class="text-end">IGV</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $nombres = ['1' => 'Enero', '2' => 'Febrero', '3' => 'Marzo', '4' => 'Abril', '5' => 'Mayo', '6' => 'Junio',
                                '7' => 'Julio', '8' => 'Agosto', '9' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'];
                @endphp
                @for($m = 1; $m <= 12; $m++)
                    @php
                        $data = $porMes[$m] ?? ['cantidad' => 0, 'total' => 0, 'igv' => 0];
                    @endphp
                    <tr>
                        <td><strong>{{ $nombres[$m] }}</strong></td>
                        <td class="text-end">{{ $data['cantidad'] }}</td>
                        <td class="text-end">S/ {{ number_format($data['igv'], 2) }}</td>
                        <td class="text-end fw-semibold">S/ {{ number_format($data['total'], 2) }}</td>
                    </tr>
                @endfor
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <th>TOTAL</th>
                    <th class="text-end">{{ $porMes->sum('cantidad') }}</th>
                    <th class="text-end">S/ {{ number_format($porMes->sum('igv'), 2) }}</th>
                    <th class="text-end">S/ {{ number_format($porMes->sum('total'), 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
