@extends('layouts.app')

@section('title', 'Clientes')
@section('header', 'Clientes')

@section('content')
<div class="page-title">
    <h2><i class="fas fa-users me-2"></i>Clientes</h2>
    <a href="{{ route('clientes.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Nuevo Cliente
    </a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small mb-1">Buscar en base local</label>
                <input type="text" name="buscar" class="form-control form-control-sm" value="{{ request('buscar') }}" placeholder="Nombre, número de documento...">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Tipo de documento</label>
                <select name="tipo_documento" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="DNI" {{ request('tipo_documento') == 'DNI' ? 'selected' : '' }}>DNI</option>
                    <option value="RUC" {{ request('tipo_documento') == 'RUC' ? 'selected' : '' }}>RUC</option>
                    <option value="CE" {{ request('tipo_documento') == 'CE' ? 'selected' : '' }}>CE</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary btn-sm w-100"><i class="fas fa-search me-1"></i>Buscar</button>
            </div>
        </form>
        <hr>
        <form id="form-buscar-decolecta" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Tipo Doc.</label>
                <select id="decolecta-tipo" class="form-select form-select-sm">
                    <option value="DNI">DNI</option>
                    <option value="RUC">RUC</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small mb-1">Número</label>
                <input type="text" id="decolecta-numero" class="form-control form-control-sm" maxlength="15" placeholder="Ej. 12345678">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success btn-sm w-100">
                    <i class="fas fa-search me-1"></i>Buscar
                </button>
            </div>
        </form>
        <div class="mt-2" id="decolecta-resultado"></div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Documento</th>
                    <th>Nombre / Razón Social</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th class="text-center">Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clientes as $cliente)
                    <tr>
                        <td>
                            <span class="badge bg-secondary">{{ $cliente->tipo_documento }}</span>
                            <code>{{ $cliente->numero_documento }}</code>
                        </td>
                        <td class="fw-medium">{{ $cliente->nombre_razon_social }}</td>
                        <td>{{ $cliente->direccion ?? '—' }}</td>
                        <td>{{ $cliente->telefono ?? '—' }}</td>
                        <td>{{ $cliente->email ?? '—' }}</td>
                        <td class="text-center">
                            @if($cliente->activo)
                                <span class="badge bg-success-subtle text-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar cliente?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay clientes</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($clientes->hasPages())
        <div class="card-footer">{{ $clientes->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
const consultaDocumentoUrl = '{{ route('consulta.documento') }}';
const clienteCsrf = '{{ csrf_token() }}';

document.getElementById('form-buscar-decolecta').addEventListener('submit', (e) => {
    e.preventDefault();
    const tipo = document.getElementById('decolecta-tipo').value;
    const numero = document.getElementById('decolecta-numero').value.trim();
    const requerido = tipo === 'DNI' ? 8 : 11;
    const resultado = document.getElementById('decolecta-resultado');

    if (!numero) { resultado.innerHTML = '<div class="alert alert-warning py-1 small mb-0">Ingresa un número.</div>'; return; }
    if (numero.length !== requerido) {
        resultado.innerHTML = `<div class="alert alert-warning py-1 small mb-0">El ${tipo} debe tener ${requerido} dígitos.</div>`;
        return;
    }

    resultado.innerHTML = '<div class="alert alert-info py-1 small mb-0">Buscando en la API...</div>';

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
            const url = "{{ route('clientes.create') }}"
                + "?prefill_tipo_documento=" + encodeURIComponent(c.tipo_documento)
                + "&prefill_numero_documento=" + encodeURIComponent(c.numero_documento)
                + "&prefill_nombre_razon_social=" + encodeURIComponent(c.nombre_razon_social)
                + "&prefill_direccion=" + encodeURIComponent(c.direccion || '');
            resultado.innerHTML = `
                <div class="alert alert-success py-2 mb-0 small">
                    <strong>${c.nombre_razon_social}</strong><br>
                    ${c.direccion ? c.direccion + '<br>' : ''}
                    <a href="${url}" class="btn btn-sm btn-primary mt-1">
                        <i class="fas fa-user-plus"></i> Crear cliente con estos datos
                    </a>
                </div>`;
        } else {
            resultado.innerHTML = `<div class="alert alert-warning py-1 small mb-0">${body.message || 'No se encontraron datos.'}</div>`;
        }
    })
    .catch(err => {
        resultado.innerHTML = '<div class="alert alert-danger py-1 small mb-0">No se pudo consultar.</div>';
    });
});
</script>
@endpush
