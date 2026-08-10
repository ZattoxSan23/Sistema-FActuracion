<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de SUNAT
    |--------------------------------------------------------------------------
    */

    'entorno' => env('SUNAT_ENV', 'beta'),
    'modo_envio' => env('SUNAT_MODO', 'ose'),

    // URLs por defecto
    'urls' => [
        'beta_ose' => 'https://ose-test.nubefact.com/api/v1',
        'beta_gre' => 'https://gre-test.nubefact.com/ol-ti-itcpe/billService',
        'prod_ose' => 'https://ose.nubefact.com/api/v1',
        'prod_gre' => 'https://gre.nubefact.com/ol-ti-itcpe/billService',
    ],

    'credenciales' => [
        'ruc' => env('SUNAT_RUC'),
        'usuario_sol' => env('SUNAT_USUARIO_SOL'),
        'clave_sol' => env('SUNAT_CLAVE_SOL'),
    ],

    'certificado' => [
        'path' => env('SUNAT_CERT_PATH', storage_path('sunat/certificate.pem')),
        'password' => env('SUNAT_CERT_PASSWORD'),
    ],

    'opciones' => [
        'envio_automatico' => env('SUNAT_ENVIO_AUTO', true),
        'intentos_max' => env('SUNAT_INTENTOS', 3),
        'timeout_segundos' => env('SUNAT_TIMEOUT', 30),
    ],
];
