<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReporteResumenDiarioExport implements FromView, WithTitle, WithStyles
{
    public function __construct(public array $datos, public string $fecha) {}

    public function view(): View
    {
        return view('reportes.exports.resumen-diario-excel', [
            'datos' => $this->datos,
            'fecha' => $this->fecha,
        ]);
    }

    public function title(): string
    {
        return 'Resumen Diario';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            3 => ['font' => ['bold' => true]],
        ];
    }
}
