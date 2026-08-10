<?php

namespace App\Exports;

use App\Models\Cliente;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReporteEstadoCuentaClienteExport implements FromView, WithTitle, WithStyles
{
    public function __construct(public Cliente $cliente, public iterable $ventas, public array $totales, public array $filtros) {}

    public function view(): View
    {
        return view('reportes.exports.cliente-cuenta-excel', [
            'cliente' => $this->cliente,
            'ventas' => $this->ventas,
            'totales' => $this->totales,
            'filtros' => $this->filtros,
        ]);
    }

    public function title(): string
    {
        return 'Estado de Cuenta';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            3 => ['font' => ['bold' => true]],
        ];
    }
}
