<?php

namespace App\Exports;

use App\Models\CropAbsorption;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CropsExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    protected int $branchId;

    public function __construct(int $branchId)
    {
        $this->branchId = $branchId;
    }

    public function title(): string
    {
        return 'Penyerapan Hasil Tani';
    }

    public function collection()
    {
        return CropAbsorption::with('member.user')
            ->where('branch_id', $this->branchId)
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'No. Referensi',
            'Nama Anggota',
            'NIK',
            'Komoditas',
            'Berat (Kg)',
            'Harga/Kg (Rp)',
            'Total Bayar (Rp)',
            'Status',
            'Tanggal Pengajuan',
            'Catatan',
        ];
    }

    public function map($crop): array
    {
        return [
            $crop->reference_number ?? ('CROP-' . $crop->id),
            $crop->member->user->name ?? '-',
            $crop->member->nik ?? '-',
            $crop->commodity_name ?? '-',
            number_format($crop->weight_kg ?? 0, 2, ',', '.'),
            number_format($crop->price_per_kg ?? 0, 2, ',', '.'),
            number_format($crop->total_payout ?? 0, 2, ',', '.'),
            ucfirst($crop->status),
            $crop->created_at?->format('d/m/Y') ?? '-',
            $crop->notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '27AE60']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
