<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArqueoDetalle extends Model
{
    protected $table = 'arqueo_detalles';

    protected $fillable = [
        'caja_id',
        'user_id',
        'denominacion',
        'cantidad',
        'subtotal',
        'fecha',
        'observaciones',
    ];

    protected $casts = [
        'denominacion' => 'decimal:2',
        'cantidad' => 'integer',
        'subtotal' => 'decimal:2',
        'fecha' => 'datetime',
    ];

    public const DENOMINACIONES = [
        '200.00' => 'S/ 200',
        '100.00' => 'S/ 100',
        '50.00' => 'S/ 50',
        '20.00' => 'S/ 20',
        '10.00' => 'S/ 10',
        '5.00' => 'S/ 5',
        '2.00' => 'S/ 2',
        '1.00' => 'S/ 1',
        '0.50' => 'S/ 0.50',
        '0.20' => 'S/ 0.20',
        '0.10' => 'S/ 0.10',
    ];

    public const DENOMINACIONES_VALORES = [200, 100, 50, 20, 10, 5, 2, 1, 0.5, 0.2, 0.1];

    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
