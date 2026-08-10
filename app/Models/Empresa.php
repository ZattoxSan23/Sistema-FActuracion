<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresa';

    protected $fillable = [
        'ruc',
        'razon_social',
        'nombre_comercial',
        'direccion',
        'ubigeo',
        'departamento',
        'provincia',
        'distrito',
        'telefono',
        'email',
        'web',
        'logo_path',
        'igv',
        'moneda',
        'tipo_precio',
        'pie_pagina_ticket',
        'mensaje_personalizado',
        'activo',
    ];

    protected $casts = [
        'igv' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public static function actual(): ?self
    {
        return self::where('activo', true)->first();
    }
}
