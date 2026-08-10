@extends('layouts.app')

@section('title', 'Nuevo Cliente')
@section('header', 'Nuevo Cliente')

@php
    $prefill = $prefill ?? [
        'tipo_documento' => old('tipo_documento', 'DNI'),
        'numero_documento' => old('numero_documento', ''),
        'nombre_razon_social' => old('nombre_razon_social', ''),
        'direccion' => old('direccion', ''),
    ];
@endphp

@section('content')
<form action="{{ route('clientes.store') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-body">
            @if(!empty($prefill['nombre_razon_social']))
                <div class="alert alert-success py-2 mb-3">
                    <i class="fas fa-check-circle me-1"></i>
                    Datos precargados desde la API. Verifica y completa la información restante.
                </div>
            @else
                <div class="alert alert-info py-2 mb-3">
                    <i class="fas fa-info-circle me-1"></i>
                    Escribe el documento y presiona el botón <strong>Buscar</strong> para autocompletar con la API del sistema (RENIEC/SUNAT).
                </div>
            @endif
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tipo Documento</label>
                    <select name="tipo_documento" class="form-select" id="cliente-tipo-doc" required>
                        <option value="DNI" {{ ($prefill['tipo_documento'] ?? 'DNI') == 'DNI' ? 'selected' : '' }}>DNI</option>
                        <option value="RUC" {{ ($prefill['tipo_documento'] ?? '') == 'RUC' ? 'selected' : '' }}>RUC</option>
                        <option value="CE" {{ ($prefill['tipo_documento'] ?? '') == 'CE' ? 'selected' : '' }}>Carnet de Extranjería</option>
                        <option value="PASAPORTE" {{ ($prefill['tipo_documento'] ?? '') == 'PASAPORTE' ? 'selected' : '' }}>Pasaporte</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Número</label>
                    <div class="input-group">
                        <input type="text" name="numero_documento" id="cliente-numero-doc" class="form-control" maxlength="15" value="{{ $prefill['numero_documento'] ?? '' }}" required>
                        <button type="button" class="btn btn-primary" id="cliente-buscar-decolecta" title="Buscar en la API">
                            <i class="fas fa-search me-1"></i>Buscar
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nombre / Razón Social</label>
                    <input type="text" name="nombre_razon_social" id="cliente-nombre" class="form-control" value="{{ $prefill['nombre_razon_social'] ?? '' }}" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" id="cliente-direccion" class="form-control" value="{{ $prefill['direccion'] ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ubigeo</label>
                    <input type="text" name="ubigeo" class="form-control" maxlength="6" value="{{ old('ubigeo') }}">
                </div>
            </div>
            <div class="mt-3" id="cliente-consulta-estado" role="status" aria-live="polite"></div>
        </div>
        <div class="card-footer">
            <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
const consultaDocumentoUrl = '{{ route('consulta.documento') }}';
const clienteCsrf = '{{ csrf_token() }}';
const estadoConsulta = document.getElementById('cliente-consulta-estado');

function setEstado(texto, clase) {
    estadoConsulta.innerHTML = texto ? `<div class="alert ${clase} py-2 mb-0">${texto}</div>` : '';
}

function buscarEnDecolecta() {
    const tipo = document.getElementById('cliente-tipo-doc').value;
    const numero = document.getElementById('cliente-numero-doc').value.trim();
    const requerido = tipo === 'DNI' ? 8 : tipo === 'RUC' ? 11 : 0;

    if (!numero) {
        setEstado('Ingresa el número de documento.', 'alert-warning');
        return;
    }
    if (requerido && numero.length !== requerido) {
        setEstado(`El ${tipo} debe tener ${requerido} dígitos.`, 'alert-warning');
        return;
    }

    setEstado('Buscando datos en la API...', 'alert-info');

    fetch(consultaDocumentoUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': clienteCsrf,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ tipo, numero }),
    })
    .then(r => r.json().then(j => ({ status: r.status, body: j })))
    .then(({ status, body }) => {
        if (body && body.success) {
            const c = body.cliente;
            document.getElementById('cliente-nombre').value = c.nombre_razon_social || '';
            document.getElementById('cliente-direccion').value = c.direccion || '';
            setEstado(`<i class="fas fa-check-circle"></i> Datos cargados: <strong>${c.nombre_razon_social}</strong>`, 'alert-success');
        } else {
            setEstado(`<i class="fas fa-exclamation-triangle"></i> ${body.message || 'No se encontraron datos. Ingréselos manualmente.'}`, 'alert-warning');
        }
    })
    .catch(err => {
        setEstado('<i class="fas fa-times-circle"></i> No se pudo consultar el servicio. Ingréselos manualmente.', 'alert-danger');
    });
}

document.getElementById('cliente-buscar-decolecta').addEventListener('click', buscarEnDecolecta);
document.getElementById('cliente-numero-doc').addEventListener('keydown', (e) => {
    if (e.key === 'Enter') { e.preventDefault(); buscarEnDecolecta(); }
});
</script>
@endpush
