<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'venta_id',
        'metodo_pago',
        'monto',
        'vuelto',
        'monto_recibido',
        'tipo_tarjeta',
        'marca_tarjeta',
        'numero_operacion',
        'numero_voucher',
        'cuenta_destino',
        'banco',
        'observaciones',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'vuelto' => 'decimal:2',
        'monto_recibido' => 'decimal:2',
    ];

    public const METODO_EFECTIVO = 'efectivo';
    public const METODO_TARJETA = 'tarjeta';
    public const METODO_YAPE = 'yape';
    public const METODO_PLIN = 'plin';
    public const METODO_TRANSFERENCIA = 'transferencia';
    public const METODO_OTROS = 'otros';

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function getMetodoLabelAttribute(): string
    {
        return match ($this->metodo_pago) {
            self::METODO_EFECTIVO => 'Efectivo',
            self::METODO_TARJETA => 'Tarjeta',
            self::METODO_YAPE => 'Yape',
            self::METODO_PLIN => 'Plin',
            self::METODO_TRANSFERENCIA => 'Transferencia',
            self::METODO_OTROS => 'Otros',
            default => ucfirst($this->metodo_pago),
        };
    }
}
