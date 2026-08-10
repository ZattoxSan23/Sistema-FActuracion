<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comprobante extends Model
{
    protected $table = 'comprobantes';

    protected $fillable = [
        'venta_id',
        'tipo_comprobante',
        'serie',
        'correlativo',
        'correlativo_completo',
        'xml_firmado',
        'xml_sin_firma',
        'hash_cpe',
        'hash_cdr',
        'firma_path',
        'cdr_path',
        'ticket',
        'codigo_respuesta',
        'descripcion_respuesta',
        'estado',
        'intentos_envio',
        'fecha_firma',
        'fecha_envio',
        'fecha_respuesta',
    ];

    protected $casts = [
        'fecha_firma' => 'datetime',
        'fecha_envio' => 'datetime',
        'fecha_respuesta' => 'datetime',
        'intentos_envio' => 'integer',
    ];

    public const ESTADO_BORRADOR = 'borrador';
    public const ESTADO_FIRMADO = 'firmado';
    public const ESTADO_ENVIADO = 'enviado';
    public const ESTADO_ACEPTADO = 'aceptado';
    public const ESTADO_RECHAZADO = 'rechazado';
    public const ESTADO_EXCEPCION = 'excepcion';
    public const ESTADO_ANULADO = 'anulado';

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function isAceptado(): bool
    {
        return $this->estado === self::ESTADO_ACEPTADO;
    }

    public function isRechazado(): bool
    {
        return $this->estado === self::ESTADO_RECHAZADO;
    }

    public function isPendiente(): bool
    {
        return in_array($this->estado, [self::ESTADO_BORRADOR, self::ESTADO_FIRMADO], true);
    }

    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado) {
            self::ESTADO_BORRADOR => 'Borrador',
            self::ESTADO_FIRMADO => 'Firmado',
            self::ESTADO_ENVIADO => 'Enviado',
            self::ESTADO_ACEPTADO => 'Aceptado',
            self::ESTADO_RECHAZADO => 'Rechazado',
            self::ESTADO_EXCEPCION => 'Excepción',
            self::ESTADO_ANULADO => 'Anulado',
            default => ucfirst($this->estado),
        };
    }
}
