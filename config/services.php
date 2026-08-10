<?php

return [
    'mail' => [
        'host' => env('MAIL_HOST', 'mailhog'),
        'port' => env('MAIL_PORT', 1025),
        'from' => [
            'address' => env('MAIL_FROM_ADDRESS', 'facturacion@example.com'),
            'name' => env('MAIL_FROM_NAME', 'Sistema de Facturación'),
        ],
    ],

    'sunat' => [
        'ruc' => env('SUNAT_RUC'),
        'usuario_sol' => env('SUNAT_USUARIO_SOL'),
        'clave_sol' => env('SUNAT_CLAVE_SOL'),
        'modo_envio' => env('SUNAT_MODO', 'ose'),
        'gre_url' => env('SUNAT_GRE_URL'),
        'ose_url' => env('SUNAT_OSE_URL'),
        'certificado_path' => env('SUNAT_CERT_PATH'),
        'certificado_password' => env('SUNAT_CERT_PASSWORD'),
    ],

    'decolecta' => [
        'url' => env('DECOLECTA_API_URL', 'https://api.decolecta.com/v1'),
        'token' => env('DECOLECTA_API_TOKEN'),
        'timeout' => (int) env('DECOLECTA_API_TIMEOUT', 10),
    ],

    'docs' => [
        'url' => env('DOCS_API_URL', 'http://146.181.39.62:3000'),
        'key' => env('DOCS_API_KEY'),
        'timeout' => (int) env('DOCS_API_TIMEOUT', 10),
    ],
];
