<?php

namespace App\Exports;

use App\Models\Loan;
use App\Models\LoanPayment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LoansExport implements
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
        return 'Laporan Pinjaman';
    }

    public function collection()
    {
        // Strict branch isolation: Always use session branch_id to prevent IDOR
        $authorizedBranchId = auth()->user()->branch_id;

        return Loan::with(['member.user', 'payments'])
            ->where('branch_id', $authorizedBranchId)
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'No. Pinjaman',
            'Nama Anggota',
            'NIK',
            'Jumlah Disetujui (Rp)',
            'Jumlah Pokok (Rp)',
            'Tenor (Bulan)',
            'Bunga (%)',
            'Status',
            'Tgl Pengajuan',
            'Tgl Disetujui',
            'Total Bayar',
            'Sisa Hutang',
        ];
    }

    public function map($loan): array
    {
        $totalPaid = $loan->payments->where('status', 'paid')->sum('amount_paid');
        $remaining = ($loan->amount_approved ?? 0) - $totalPaid;

        return [
            $loan->loan_number ?? ('LOAN-' . $loan->id),
            $loan->member->user->name ?? '-',
            $loan->member->nik ?? '-',
            number_format($loan->amount_approved ?? 0, 2, ',', '.'),
            number_format($loan->principal_amount ?? $loan->amount_approved ?? 0, 2, ',', '.'),
            $loan->tenor_months ?? '-',
            $loan->interest_rate ?? '-',
            ucfirst($loan->status),
            $loan->created_at?->format('d/m/Y') ?? '-',
            $loan->approved_at ? \Carbon\Carbon::parse($loan->approved_at)->format('d/m/Y') : '-',
            number_format($totalPaid, 2, ',', '.'),
            number_format(max(0, $remaining), 2, ',', '.'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C0392B']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
