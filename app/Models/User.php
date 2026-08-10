<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    public const ROL_ADMIN = 'administrador';
    public const ROL_CAJERA = 'cajera';
    public const ROL_CONTADOR = 'contador';

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'dni',
        'telefono',
        'direccion',
        'activo',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    public function cajasAbiertas(): HasMany
    {
        return $this->hasMany(Caja::class, 'user_id_apertura');
    }

    public function cajasCerradas(): HasMany
    {
        return $this->hasMany(Caja::class, 'user_id_cierre');
    }

    public function movimientosCaja(): HasMany
    {
        return $this->hasMany(CajaMovimiento::class);
    }

    public function cajaAbierta(): ?Caja
    {
        return Caja::where('user_id_apertura', $this->id)
            ->where('estado', 'abierta')
            ->first();
    }

    public function isAdmin(): bool
    {
        return $this->rol === self::ROL_ADMIN;
    }

    public function isCajera(): bool
    {
        return $this->rol === self::ROL_CAJERA;
    }

    public function isContador(): bool
    {
        return $this->rol === self::ROL_CONTADOR;
    }

    public function hasRole(string|array $rol): bool
    {
        if (is_array($rol)) {
            return in_array($this->rol, $rol, true);
        }
        return $this->rol === $rol;
    }

    public static function rolesDisponibles(): array
    {
        return [
            self::ROL_ADMIN => 'Administrador',
            self::ROL_CAJERA => 'Cajera',
            self::ROL_CONTADOR => 'Contador',
        ];
    }
}
