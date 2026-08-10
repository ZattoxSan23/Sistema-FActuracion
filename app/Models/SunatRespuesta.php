<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SunatRespuesta extends Model
{
    protected $table = 'sunat_respuestas';

    public $timestamps = true;

    protected $fillable = [
        'comprobante_id',
        'tipo_operacion',
        'endpoint',
        'request_payload',
        'response_payload',
        'http_status',
        'codigo_respuesta',
        'descripcion',
        'exito',
        'duracion_ms',
        'ip_origen',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
        'http_status' => 'integer',
        'exito' => 'boolean',
        'duracion_ms' => 'integer',
    ];

    public function comprobante(): BelongsTo
    {
        return $this->belongsTo(Comprobante::class);
    }
}
