<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VentaItem extends Model
{
    protected $table = 'venta_items';

    protected $fillable = [
        'venta_id',
        'producto_id',
        'orden',
        'unidad_medida',
        'codigo_producto',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'precio_unitario_con_igv',
        'valor_unitario',
        'descuento',
        'subtotal',
        'igv_item',
        'total_item',
        'tipo_afectacion_igv',
        'gratuito',
        'notas',
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
        'precio_unitario' => 'decimal:4',
        'precio_unitario_con_igv' => 'decimal:4',
        'valor_unitario' => 'decimal:4',
        'descuento' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'igv_item' => 'decimal:2',
        'total_item' => 'decimal:2',
        'gratuito' => 'boolean',
    ];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
