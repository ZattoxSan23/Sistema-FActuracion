<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Serie extends Model
{
    protected $table = 'series';

    protected $fillable = [
        'tipo_comprobante',
        'serie',
        'correlativo_actual',
        'correlativo_desde',
        'correlativo_hasta',
        'activo',
        'principal',
        'notas',
    ];

    protected $casts = [
        'correlativo_actual' => 'integer',
        'correlativo_desde' => 'integer',
        'correlativo_hasta' => 'integer',
        'activo' => 'boolean',
        'principal' => 'boolean',
    ];

    public const TIPO_FACTURA = '01';
    public const TIPO_BOLETA = '03';
    public const TIPO_NOTA_CREDITO = '07';
    public const TIPO_NOTA_DEBITO = '08';
    public const TIPO_GUIA_REMISION = '09';
    public const TIPO_CONSTANCIA = '40';

    public const TIPOS_COMPROBANTE = [
        self::TIPO_FACTURA => 'Factura',
        self::TIPO_BOLETA => 'Boleta',
        self::TIPO_NOTA_CREDITO => 'Nota de Crédito',
        self::TIPO_NOTA_DEBITO => 'Nota de Débito',
        self::TIPO_GUIA_REMISION => 'Guía de Remisión',
        self::TIPO_CONSTANCIA => 'Constancia',
    ];

    public static function tiposConCodigoSunat(): array
    {
        return self::TIPOS_COMPROBANTE;
    }

    /**
     * Obtiene la siguiente serie/correlativo de manera atómica (con lock).
     */
    public static function siguienteCorrelativo(string $tipoComprobante, string $serie): array
    {
        return DB::transaction(function () use ($tipoComprobante, $serie) {
            $registro = self::where('tipo_comprobante', $tipoComprobante)
                ->where('serie', $serie)
                ->where('activo', true)
                ->lockForUpdate()
                ->first();

            if (!$registro) {
                throw new \RuntimeException("No existe la serie {$serie} para el tipo {$tipoComprobante}");
            }

            if ($registro->correlativo_actual >= $registro->correlativo_hasta) {
                throw new \RuntimeException("Se agotó la numeración para la serie {$serie}");
            }

            $registro->correlativo_actual++;
            $registro->save();

            return [
                'serie' => $registro->serie,
                'correlativo' => $registro->correlativo_actual,
                'correlativo_completo' => sprintf(
                    '%s-%s',
                    $registro->serie,
                    str_pad((string) $registro->correlativo_actual, 8, '0', STR_PAD_LEFT)
                ),
            ];
        });
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
