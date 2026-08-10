<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Venta extends Model
{
    protected $table = 'ventas';

    protected $fillable = [
        'caja_id',
        'cliente_id',
        'user_id',
        'correlativo',
        'tipo_comprobante',
        'serie',
        'numero',
        'fecha_emision',
        'moneda',
        'op_gravadas',
        'op_exoneradas',
        'op_inafectas',
        'op_gratuitas',
        'descuento_global',
        'igv',
        'total',
        'estado',
        'estado_sunat',
        'motivo_anulacion',
        'user_id_anulacion',
        'fecha_anulacion',
        'observaciones',
    ];

    protected $casts = [
        'fecha_emision' => 'datetime',
        'fecha_anulacion' => 'datetime',
        'op_gravadas' => 'decimal:2',
        'op_exoneradas' => 'decimal:2',
        'op_inafectas' => 'decimal:2',
        'op_gratuitas' => 'decimal:2',
        'descuento_global' => 'decimal:2',
        'igv' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function usuarioAnulacion(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_anulacion');
    }

    public function items(): HasMany
    {
        return $this->hasMany(VentaItem::class)->orderBy('orden');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function comprobante(): HasOne
    {
        return $this->hasOne(Comprobante::class);
    }

    public function getTipoComprobanteLabelAttribute(): string
    {
        return match ($this->tipo_comprobante) {
            Serie::TIPO_FACTURA => 'FACTURA',
            Serie::TIPO_BOLETA => 'BOLETA',
            Serie::TIPO_NOTA_CREDITO => 'NOTA DE CRÉDITO',
            Serie::TIPO_NOTA_DEBITO => 'NOTA DE DÉBITO',
            default => $this->tipo_comprobante,
        };
    }

    public function getTotalPagadoAttribute(): float
    {
        return (float) $this->pagos()->sum('monto');
    }

    public function getTotalVueltoAttribute(): float
    {
        return (float) $this->pagos()->sum('vuelto');
    }

    public function esFactura(): bool
    {
        return $this->tipo_comprobante === Serie::TIPO_FACTURA;
    }

    public function esBoleta(): bool
    {
        return $this->tipo_comprobante === Serie::TIPO_BOLETA;
    }

    public function scopeDelDia(Builder $query): Builder
    {
        return $query->whereDate('fecha_emision', today());
    }

    public function scopeEntreFechas(Builder $query, $desde, $hasta): Builder
    {
        return $query->whereBetween('fecha_emision', [$desde, $hasta]);
    }

    public function scopeNoAnuladas(Builder $query): Builder
    {
        return $query->where('estado', '!=', 'anulada');
    }

    public function scopeEmitidas(Builder $query): Builder
    {
        return $query->whereIn('estado', ['registrada', 'emitida', 'aceptado']);
    }

    public function scopePorTipo(Builder $query, string $tipo): Builder
    {
        return $query->where('tipo_comprobante', $tipo);
    }
}
