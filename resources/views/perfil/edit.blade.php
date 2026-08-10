@extends('layouts.app')

@section('title', 'Mi Perfil')
@section('header', 'Mi Perfil')

@section('content')
<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="fas fa-user me-2"></i>Información Personal</div>
            <div class="card-body">
                <form action="{{ route('perfil.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">DNI</label>
                        <input type="text" name="dni" class="form-control" value="{{ $user->dni }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" value="{{ $user->telefono }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control" value="{{ $user->direccion }}">
                    </div>
                    <div class="alert alert-info">
                        <strong>Rol:</strong> {{ ucfirst($user->rol) }}
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Guardar</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="fas fa-lock me-2"></i>Cambiar Contraseña</div>
            <div class="card-body">
                <form action="{{ route('perfil.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Contraseña Actual</label>
                        <input type="password" name="password_actual" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nueva Contraseña</label>
                        <input type="password" name="password_nuevo" class="form-control" minlength="6" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmar Nueva Contraseña</label>
                        <input type="password" name="password_nuevo_confirmation" class="form-control" minlength="6" required>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-key me-1"></i>Cambiar Contraseña</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
