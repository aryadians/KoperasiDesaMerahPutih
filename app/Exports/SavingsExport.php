<?php

namespace App\Exports;

use App\Models\MemberSaving;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SavingsExport implements
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
        return 'Buku Simpanan Anggota';
    }

    public function collection()
    {
        // Strict branch isolation: Always use session branch_id to prevent IDOR
        $authorizedBranchId = auth()->user()->branch_id;

        return MemberSaving::with('member.user')
            ->whereHas('member.user', function ($q) use ($authorizedBranchId) {
                $q->where('branch_id', $authorizedBranchId);
            })
            ->latest('transaction_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Anggota',
            'NIK',
            'Jenis Simpanan',
            'Jumlah (Rp)',
            'Keterangan',
        ];
    }

    public function map($saving): array
    {
        return [
            \Carbon\Carbon::parse($saving->transaction_date)->format('d/m/Y'),
            $saving->member->user->name ?? '-',
            $saving->member->nik ?? '-',
            ucfirst($saving->type),
            number_format($saving->amount, 2, ',', '.'),
            $saving->notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2980B9']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
