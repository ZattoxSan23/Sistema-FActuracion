@extends('layouts.app')

@section('title', 'Respuestas SUNAT')
@section('header', 'Log de Respuestas SUNAT')

@section('content')
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Comprobante</th>
                    <th>HTTP</th>
                    <th>Código</th>
                    <th>Duración</th>
                    <th>Éxito</th>
                </tr>
            </thead>
            <tbody>
                @forelse($respuestas as $r)
                    <tr>
                        <td>{{ $r->created_at->format('d/m/Y H:i:s') }}</td>
                        <td><span class="badge bg-secondary">{{ $r->tipo_operacion }}</span></td>
                        <td><code>{{ $r->comprobante?->correlativo_completo ?? '—' }}</code></td>
                        <td>{{ $r->http_status }}</td>
                        <td><code>{{ $r->codigo_respuesta ?? '—' }}</code></td>
                        <td>{{ $r->duracion_ms }}ms</td>
                        <td>
                            @if($r->exito)
                                <span class="badge bg-success">OK</span>
                            @else
                                <span class="badge bg-danger">Error</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Sin respuestas</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($respuestas->hasPages())
        <div class="card-footer">{{ $respuestas->links() }}</div>
    @endif
</div>
@endsection
