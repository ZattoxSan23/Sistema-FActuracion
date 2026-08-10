<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SunatConfig extends Model
{
    protected $table = 'sunat_config';

    protected $fillable = [
        'entorno',
        'modo_envio',
        'gre_url',
        'ose_url',
        'usuario_sol',
        'clave_sol',
        'certificado_path',
        'certificado_password',
        'certificado_vence',
        'token_ose',
        'token_ose_vence',
        'envio_automatico',
        'intentos_max',
        'timeout_segundos',
        'notas',
    ];

    protected $casts = [
        'envio_automatico' => 'boolean',
        'token_ose_vence' => 'datetime',
        'intentos_max' => 'integer',
        'timeout_segundos' => 'integer',
    ];

    public static function actual(): ?self
    {
        return self::first();
    }

    public function isBeta(): bool
    {
        return $this->entorno === 'beta';
    }

    public function isProduccion(): bool
    {
        return $this->entorno === 'produccion';
    }
}
