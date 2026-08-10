@extends('layouts.app')

@section('title', 'Usuarios')
@section('header', 'Usuarios')

@section('content')
<div class="page-title">
    <h2><i class="fas fa-user-shield me-2"></i>Usuarios</h2>
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Nuevo Usuario
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>DNI</th>
                    <th>Teléfono</th>
                    <th class="text-center">Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $u)
                    <tr>
                        <td class="fw-medium">{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td>
                            @php
                                $colores = ['administrador' => 'danger', 'cajera' => 'success', 'contador' => 'info'];
                                $color = $colores[$u->rol] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }}">{{ ucfirst($u->rol) }}</span>
                        </td>
                        <td>{{ $u->dni ?? '—' }}</td>
                        <td>{{ $u->telefono ?? '—' }}</td>
                        <td class="text-center">
                            @if($u->activo)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('usuarios.edit', $u) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('usuarios.toggle', $u) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-outline-{{ $u->activo ? 'warning' : 'success' }}">
                                    <i class="fas fa-{{ $u->activo ? 'ban' : 'check' }}"></i>
                                </button>
                            </form>
                            @if($u->id !== auth()->id())
                                <form action="{{ route('usuarios.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($usuarios->hasPages())
        <div class="card-footer">{{ $usuarios->links() }}</div>
    @endif
</div>
@endsection
