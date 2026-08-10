@extends('layouts.app')

@section('title', 'Apertura de Caja')
@section('header', 'Aperturar Caja')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <i class="fas fa-cash-register me-2"></i>Apertura de Caja
            </div>
            <div class="card-body">
                <form action="{{ route('caja.apertura.store') }}" method="POST">
                    @csrf

                    <div class="text-center mb-4">
                        <i class="fas fa-cash-register" style="font-size: 4rem; color: #10b981;"></i>
                        <h4 class="mt-3">{{ auth()->user()->name }}</h4>
                        <p class="text-muted">{{ now()->format('d/m/Y H:i:s') }}</p>
                    </div>

                    <div class="mb-3">
                        <label for="monto_apertura" class="form-label">Monto de apertura (efectivo en caja)</label>
                        <div class="input-group">
                            <span class="input-group-text">S/</span>
                            <input type="number" step="0.01" min="0"
                                   name="monto_apertura" id="monto_apertura"
                                   class="form-control form-control-lg text-end @error('monto_apertura') is-invalid @enderror"
                                   value="{{ old('monto_apertura', '0.00') }}" required>
                        </div>
                        @error('monto_apertura')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones (opcional)</label>
                        <textarea name="observaciones" id="observaciones" rows="3"
                                  class="form-control"
                                  placeholder="Notas sobre la apertura...">{{ old('observaciones') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100">
                        <i class="fas fa-check me-2"></i>APERTURAR CAJA
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
