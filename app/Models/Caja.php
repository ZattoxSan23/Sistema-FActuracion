<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caja extends Model
{
    protected $table = 'cajas';

    protected $fillable = [
        'user_id_apertura',
        'user_id_cierre',
        'fecha_apertura',
        'fecha_cierre',
        'monto_apertura',
        'monto_efectivo_teorico',
        'monto_efectivo_real',
        'diferencia',
        'total_ingresos',
        'total_egresos',
        'total_ventas_efectivo',
        'total_ventas_tarjeta',
        'total_ventas_yape',
        'total_ventas_transferencia',
        'cantidad_ventas',
        'estado',
        'observaciones_apertura',
        'observaciones_cierre',
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
        'monto_apertura' => 'decimal:2',
        'monto_efectivo_teorico' => 'decimal:2',
        'monto_efectivo_real' => 'decimal:2',
        'diferencia' => 'decimal:2',
        'total_ingresos' => 'decimal:2',
        'total_egresos' => 'decimal:2',
        'total_ventas_efectivo' => 'decimal:2',
        'total_ventas_tarjeta' => 'decimal:2',
        'total_ventas_yape' => 'decimal:2',
        'total_ventas_transferencia' => 'decimal:2',
        'cantidad_ventas' => 'integer',
    ];

    public function usuarioApertura(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_apertura');
    }

    public function usuarioCierre(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_cierre');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(CajaMovimiento::class);
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    public function arqueoDetalles(): HasMany
    {
        return $this->hasMany(ArqueoDetalle::class);
    }

    public function scopeAbiertas(Builder $query): Builder
    {
        return $query->where('estado', 'abierta');
    }

    public function scopeCerradas(Builder $query): Builder
    {
        return $query->where('estado', 'cerrada');
    }

    public function scopeDelDia(Builder $query): Builder
    {
        return $query->whereDate('fecha_apertura', today());
    }

    public function scopeEntreFechas(Builder $query, $desde, $hasta): Builder
    {
        return $query->whereBetween('fecha_apertura', [$desde, $hasta]);
    }

    public function estaAbierta(): bool
    {
        return $this->estado === 'abierta';
    }

    public static function cajaAbierta(): ?self
    {
        return self::where('estado', 'abierta')
            ->orderByDesc('fecha_apertura')
            ->first();
    }

    /**
     * Recalcula totales en base a movimientos.
     */
    public function recalcularTotales(): void
    {
        $movimientos = $this->movimientos()->get();
        $ventas = $this->ventas()->where('estado', '!=', 'anulada')->get();

        $this->total_ingresos = $movimientos->where('tipo', 'ingreso')->sum('monto');
        $this->total_egresos = $movimientos->where('tipo', 'egreso')->sum('monto');
        $this->cantidad_ventas = $ventas->count();
        $this->total_ventas_efectivo = $ventas->whereIn('estado', ['registrada', 'emitida', 'aceptado'])->sum(fn ($v) =>
            $v->pagos->where('metodo_pago', 'efectivo')->sum('monto')
        );
        $this->total_ventas_tarjeta = $ventas->sum(fn ($v) =>
            $v->pagos->where('metodo_pago', 'tarjeta')->sum('monto')
        );
        $this->total_ventas_yape = $ventas->sum(fn ($v) =>
            $v->pagos->whereIn('metodo_pago', ['yape', 'plin'])->sum('monto')
        );
        $this->total_ventas_transferencia = $ventas->sum(fn ($v) =>
            $v->pagos->where('metodo_pago', 'transferencia')->sum('monto')
        );

        $this->monto_efectivo_teorico = round(
            (float) $this->monto_apertura
            + (float) $this->total_ventas_efectivo
            + (float) $this->total_ingresos
            - (float) $this->total_egresos,
            2
        );

        $this->save();
    }
}
