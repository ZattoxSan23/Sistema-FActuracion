<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tipo_documento',
        'numero_documento',
        'nombre_razon_social',
        'direccion',
        'ubigeo',
        'telefono',
        'email',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public const TIPO_DNI = 'DNI';
    public const TIPO_RUC = 'RUC';
    public const TIPO_CE = 'CE';
    public const TIPO_PASAPORTE = 'PASAPORTE';
    public const TIPO_SIN_DOCUMENTO = 'SIN_DOCUMENTO';

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    public function getDocumentoCompletoAttribute(): string
    {
        return "{$this->tipo_documento}: {$this->numero_documento}";
    }

    public static function clienteVarios(): ?self
    {
        return self::where('tipo_documento', self::TIPO_SIN_DOCUMENTO)->first();
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeBuscar(Builder $query, string $termino): Builder
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombre_razon_social', 'ILIKE', "%{$termino}%")
              ->orWhere('numero_documento', 'ILIKE', "%{$termino}%");
        });
    }

    /**
     * Crea o retorna el cliente por defecto "Varios"
     */
    public static function getOrCreateVarios(): self
    {
        return self::firstOrCreate(
            [
                'tipo_documento' => self::TIPO_SIN_DOCUMENTO,
                'numero_documento' => '00000000',
            ],
            [
                'nombre_razon_social' => 'CLIENTES VARIOS',
                'activo' => true,
            ]
        );
    }
}
