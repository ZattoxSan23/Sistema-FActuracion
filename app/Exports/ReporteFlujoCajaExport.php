<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReporteFlujoCajaExport implements FromView, WithTitle, WithStyles
{
    public function __construct(public array $datos, public array $totales, public array $filtros) {}

    public function view(): View
    {
        return view('reportes.exports.flujo-caja-excel', [
            'datos' => $this->datos,
            'totales' => $this->totales,
            'filtros' => $this->filtros,
        ]);
    }

    public function title(): string
    {
        return 'Flujo de Caja';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            3 => ['font' => ['bold' => true]],
        ];
    }
}
