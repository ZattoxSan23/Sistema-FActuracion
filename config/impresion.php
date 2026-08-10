<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de Impresoras
    |--------------------------------------------------------------------------
    */

    'ticket' => [
        'nombre' => env('PRINTER_TICKET_NAME', 'Impresora Tickets'),
        'path' => env('PRINTER_TICKET_PATH', '/dev/usb/lp0'),
        'caracteres_por_linea' => 42, // 80mm = 42 caracteres
        'cortar_papel' => true,
        'logo_path' => null,
    ],

    'formatos' => [
        'ticket' => [
            'nombre' => 'Ticket 80mm',
            'descripcion' => 'Para impresoras térmicas de tickets',
            'papel' => '80mm',
            'controlador' => 'escpos',
        ],
        'a4' => [
            'nombre' => 'A4',
            'descripcion' => 'Hoja A4 completa',
            'papel' => 'A4',
            'controlador' => 'pdf',
        ],
        'a5' => [
            'nombre' => 'A5',
            'descripcion' => 'Media hoja A5',
            'papel' => 'A5',
            'controlador' => 'pdf',
        ],
    ],

    'formato_default' => env('PRINTER_DEFAULT_FORMAT', 'ticket'),
];
