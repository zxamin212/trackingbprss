<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanBerkasExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithColumnFormatting,
    WithStyles,
    ShouldAutoSize
{
    private int $no = 0;

    public function __construct(private Collection $berkas)
    {
    }

    public function collection(): Collection
    {
        return $this->berkas;
    }

    public function headings(): array
    {
        return [
            'No', 'No. Berkas', 'Nama Nasabah', 'Jenis Kredit', 'Plafon (Rp)',
            'Kantor', 'SLO', 'Sumber', 'Tanggal Masuk', 'Tanggal Selesai',
            'Lama (hari)', 'Status',
        ];
    }

    public function map($b): array
    {
        return [
            ++$this->no,
            $b->nomor_berkas,
            $b->nama_nasabah,
            $b->jenis_kredit,
            (float) $b->plafon,
            $b->kantor->nama_kantor ?? '-',
            $b->slo->name ?? '-',
            $b->sumber === 'marketing' ? 'Marketing' : 'Langsung',
            $b->tanggal_masuk?->format('d-m-Y'),
            $b->tanggal_selesai?->format('d-m-Y') ?? '-',
            $b->lama_hari,
            ucfirst(str_replace('_', ' ', $b->status_terkini)),
        ];
    }

    public function columnFormats(): array
    {
        return ['E' => '#,##0'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}