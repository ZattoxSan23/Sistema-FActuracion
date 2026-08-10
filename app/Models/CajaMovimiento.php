<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CajaMovimiento extends Model
{
    protected $table = 'caja_movimientos';

    protected $fillable = [
        'caja_id',
        'user_id',
        'tipo',
        'monto',
        'metodo_pago',
        'concepto',
        'referencia',
        'venta_id',
        'notas',
        'fecha',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha' => 'datetime',
    ];

    public const TIPO_INGRESO = 'ingreso';
    public const TIPO_EGRESO = 'egreso';
    public const TIPO_VENTA = 'venta';
    public const TIPO_RETIRO = 'retiro';
    public const TIPO_DEPOSITO = 'deposito';

    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function getTipoLabelAttribute(): string
    {
        return match ($this->tipo) {
            self::TIPO_INGRESO => 'Ingreso',
            self::TIPO_EGRESO => 'Egreso',
            self::TIPO_VENTA => 'Venta',
            self::TIPO_RETIRO => 'Retiro',
            self::TIPO_DEPOSITO => 'Depósito',
            default => ucfirst($this->tipo),
        };
    }
}
