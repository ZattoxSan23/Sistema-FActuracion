<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'categoria_id',
        'codigo',
        'codigo_barra',
        'nombre',
        'descripcion',
        'unidad_medida',
        'precio_compra',
        'precio_venta',
        'precio_mayorista',
        'tipo_afectacion_igv',
        'incluye_igv',
        'activo',
        'visible_pos',
        'imagen',
        'orden',
        'stock',
        'stock_minimo',
    ];

    protected $casts = [
        'precio_compra' => 'decimal:4',
        'precio_venta' => 'decimal:4',
        'precio_mayorista' => 'decimal:4',
        'stock' => 'decimal:2',
        'stock_minimo' => 'decimal:2',
        'activo' => 'boolean',
        'visible_pos' => 'boolean',
        'incluye_igv' => 'boolean',
        'orden' => 'integer',
    ];

    public const AFECT_GRAVADO = '10';
    public const AFECT_EXONERADO = '20';
    public const AFECT_INAFECTO = '30';
    public const AFECT_EXPORTACION = '40';

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function ventaItems(): HasMany
    {
        return $this->hasMany(VentaItem::class);
    }

    public function getPrecioSinIgvAttribute(): float
    {
        if (!$this->incluye_igv) {
            return (float) $this->precio_venta;
        }
        $igv = (float) (Empresa::actual()?->igv ?? 18);
        return round((float) $this->precio_venta / (1 + ($igv / 100)), 4);
    }

    public function getIgvUnitarioAttribute(): float
    {
        if (!$this->incluye_igv || $this->tipo_afectacion_igv !== self::AFECT_GRAVADO) {
            return 0;
        }
        return round((float) $this->precio_venta - $this->precio_sin_igv, 4);
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeVisiblesPos(Builder $query): Builder
    {
        return $query->where('activo', true)->where('visible_pos', true);
    }

    public function scopeBuscar(Builder $query, string $termino): Builder
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombre', 'ILIKE', "%{$termino}%")
              ->orWhere('codigo', 'ILIKE', "%{$termino}%")
              ->orWhere('codigo_barra', 'ILIKE', "%{$termino}%");
        });
    }

    public function scopePorCategoria(Builder $query, $categoriaId): Builder
    {
        return $query->when($categoriaId, fn ($q) => $q->where('categoria_id', $categoriaId));
    }

    public function byCodigo(string $codigo): ?self
    {
        return self::where('codigo', $codigo)
            ->orWhere('codigo_barra', $codigo)
            ->first();
    }
}
