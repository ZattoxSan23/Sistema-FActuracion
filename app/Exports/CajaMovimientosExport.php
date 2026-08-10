<?php

namespace App\Exports;

use App\Models\Caja;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CajaMovimientosExport implements FromView, WithTitle, WithStyles
{
    public function __construct(public Caja $caja) {}

    public function view(): View
    {
        $this->caja->load(['usuarioApertura', 'usuarioCierre', 'movimientos.usuario']);
        return view('caja.exports.movimientos-excel', [
            'caja' => $this->caja,
        ]);
    }

    public function title(): string
    {
        return 'Caja #' . $this->caja->id;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            3 => ['font' => ['bold' => true]],
        ];
    }
}
