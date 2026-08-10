@extends('layouts.app')

@section('title', 'Configuración')
@section('header', 'Configuración General')

@section('content')
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="card-header"><i class="fas fa-building me-2"></i>Datos de la Empresa</div>
            <div class="card-body">
                <form action="{{ route('configuracion.empresa.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">RUC</label>
                            <input type="text" name="ruc" class="form-control" value="{{ $empresa->ruc }}" maxlength="11" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Razón Social</label>
                            <input type="text" name="razon_social" class="form-control" value="{{ $empresa->razon_social }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Nombre Comercial</label>
                            <input type="text" name="nombre_comercial" class="form-control" value="{{ $empresa->nombre_comercial }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Dirección</label>
                            <input type="text" name="direccion" class="form-control" value="{{ $empresa->direccion }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Ubigeo</label>
                            <input type="text" name="ubigeo" class="form-control" value="{{ $empresa->ubigeo }}" maxlength="6">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Departamento</label>
                            <input type="text" name="departamento" class="form-control" value="{{ $empresa->departamento }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Provincia</label>
                            <input type="text" name="provincia" class="form-control" value="{{ $empresa->provincia }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Distrito</label>
                            <input type="text" name="distrito" class="form-control" value="{{ $empresa->distrito }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" value="{{ $empresa->telefono }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $empresa->email }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Web</label>
                            <input type="url" name="web" class="form-control" value="{{ $empresa->web }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">IGV (%)</label>
                            <input type="number" step="0.01" name="igv" class="form-control" value="{{ $empresa->igv ?? 18 }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Moneda</label>
                            <select name="moneda" class="form-select" required>
                                <option value="PEN" {{ $empresa->moneda == 'PEN' ? 'selected' : '' }}>Soles (PEN)</option>
                                <option value="USD" {{ $empresa->moneda == 'USD' ? 'selected' : '' }}>Dólares (USD)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Precio</label>
                            <select name="tipo_precio" class="form-select">
                                <option value="incluye_igv" {{ $empresa->tipo_precio == 'incluye_igv' ? 'selected' : '' }}>Precios incluyen IGV</option>
                                <option value="no_incluye_igv" {{ $empresa->tipo_precio == 'no_incluye_igv' ? 'selected' : '' }}>Precios no incluyen IGV</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Pie de página del Ticket</label>
                            <textarea name="pie_pagina_ticket" class="form-control" rows="2">{{ $empresa->pie_pagina_ticket }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Mensaje personalizado</label>
                            <input type="text" name="mensaje_personalizado" class="form-control" value="{{ $empresa->mensaje_personalizado }}">
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><i class="fas fa-print me-2"></i>Configuración de Impresoras</div>
            <div class="card-body">
                <form action="{{ route('configuracion.impresoras.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre Impresora Tickets (Windows)</label>
                            <input type="text" class="form-control" name="printer_name" value="{{ env('PRINTER_TICKET_NAME') }}" placeholder="Ej: EPSON TM-T20">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ruta Linux</label>
                            <input type="text" class="form-control" name="printer_path" value="{{ env('PRINTER_TICKET_PATH') }}" placeholder="/dev/usb/lp0">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Formato por defecto</label>
                            <select class="form-select" name="default_format">
                                <option value="ticket" {{ env('PRINTER_DEFAULT_FORMAT') == 'ticket' ? 'selected' : '' }}>Ticket 80mm (Térmica)</option>
                                <option value="a4" {{ env('PRINTER_DEFAULT_FORMAT') == 'a4' ? 'selected' : '' }}>A4 (PDF)</option>
                                <option value="a5" {{ env('PRINTER_DEFAULT_FORMAT') == 'a5' ? 'selected' : '' }}>A5 (PDF)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Guardar</button>
                    </div>
                </form>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Configure las impresoras según el sistema operativo. En Linux, asegúrese de tener permisos sobre el dispositivo.
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span><i class="fas fa-list-ol me-2"></i>Series de Comprobantes</span>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalSerie">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tipo</th>
                            <th>Serie</th>
                            <th class="text-end">Actual</th>
                            <th class="text-end">Hasta</th>
                            <th class="text-center">Activo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($series as $s)
                            <tr>
                                <td>{{ \App\Models\Serie::TIPOS_COMPROBANTE[$s->tipo_comprobante] ?? $s->tipo_comprobante }}</td>
                                <td><code>{{ $s->serie }}</code></td>
                                <td class="text-end">{{ str_pad($s->correlativo_actual, 8, '0', STR_PAD_LEFT) }}</td>
                                <td class="text-end">{{ str_pad($s->correlativo_hasta, 8, '0', STR_PAD_LEFT) }}</td>
                                <td class="text-center">
                                    @if($s->activo)
                                        <span class="badge bg-success">Sí</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">Sin series</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSerie" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('configuracion.series.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Serie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Tipo de Comprobante</label>
                        <select name="tipo_comprobante" class="form-select" required>
                            <option value="03">03 - Boleta de Venta</option>
                            <option value="01">01 - Factura</option>
                            <option value="07">07 - Nota de Crédito</option>
                            <option value="08">08 - Nota de Débito</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Serie (4 caracteres)</label>
                        <input type="text" name="serie" class="form-control" maxlength="4" placeholder="B001" required>
                    </div>
                    <div class="row g-2">
                        <div class="col">
                            <label class="form-label">Correlativo Desde</label>
                            <input type="number" name="correlativo_desde" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Hasta</label>
                            <input type="number" name="correlativo_hasta" class="form-control" value="99999999" min="1" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
