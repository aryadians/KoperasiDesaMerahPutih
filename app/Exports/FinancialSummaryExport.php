<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\Loan;
use App\Models\CropAbsorption;
use App\Models\MemberSaving;
use App\Models\Member;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Financial Summary Export — Multi-sheet workbook.
 * Sheet 1: Ringkasan Keuangan (summary KPIs)
 * Sheet 2: Rekap Penjualan Gerai
 * Sheet 3: Rekap Simpan Pinjam
 * Sheet 4: Rekap Penyerapan Hasil Tani
 */
class FinancialSummaryExport implements WithMultipleSheets
{
    protected int $branchId;
    protected string $year;

    public function __construct(int $branchId, string $year)
    {
        $this->branchId = $branchId;
        $this->year     = $year;
    }

    public function sheets(): array
    {
        return [
            new FinancialSummarySheet($this->branchId, $this->year),
            new SalesSummarySheet($this->branchId, $this->year),
            new LoansSummarySheet($this->branchId, $this->year),
            new CropsSummarySheet($this->branchId, $this->year),
        ];
    }
}

// ─── Sheet: Ringkasan Keuangan ─────────────────────────────────────────────
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class FinancialSummarySheet implements FromArray, WithTitle, WithStyles, ShouldAutoSize
{
    protected int $branchId;
    protected string $year;

    public function __construct(int $branchId, string $year)
    {
        $this->branchId = $branchId;
        $this->year     = $year;
    }

    public function title(): string { return 'Ringkasan Keuangan'; }

    public function array(): array
    {
        $totalSales    = Order::where('branch_id', $this->branchId)->where('payment_status', 'paid')->whereYear('created_at', $this->year)->sum('total_amount');
        $totalCrop     = CropAbsorption::where('branch_id', $this->branchId)->where('status', 'paid')->whereYear('created_at', $this->year)->sum('total_payout');
        $totalLoansOut = Loan::where('branch_id', $this->branchId)->where('status', 'active')->whereYear('created_at', $this->year)->sum('amount_approved');
        $totalSavingsIn= MemberSaving::whereHas('member.user', fn($q) => $q->where('branch_id', $this->branchId))->where('amount', '>', 0)->whereYear('transaction_date', $this->year)->sum('amount');
        $activeMembers = Member::whereHas('user', fn($q) => $q->where('branch_id', $this->branchId))->where('status_aktif', true)->count();

        return [
            ['LAPORAN KEUANGAN KOPERASI DESA MERAH PUTIH', '', ''],
            ['Periode Tahun', $this->year, ''],
            ['Dicetak pada', now()->format('d/m/Y H:i'), ''],
            ['', '', ''],
            ['INDIKATOR', 'NILAI (Rp)', 'KETERANGAN'],
            ['Total Penjualan Gerai',     number_format($totalSales, 2, ',', '.'),    'Transaksi terbayar'],
            ['Total Penyerapan Hasil Tani', number_format($totalCrop, 2, ',', '.'),   'Status: paid'],
            ['Total Pinjaman Aktif',      number_format($totalLoansOut, 2, ',', '.'), 'Status: active'],
            ['Total Simpanan Masuk',      number_format($totalSavingsIn, 2, ',', '.'), 'Semua jenis simpanan'],
            ['Jumlah Anggota Aktif',      $activeMembers . ' orang',                  ''],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            5 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C0392B']]],
        ];
    }
}

// ─── Sheet: Rekap Penjualan ─────────────────────────────────────────────────
class SalesSummarySheet implements FromArray, WithTitle, ShouldAutoSize
{
    protected int $branchId;
    protected string $year;

    public function __construct(int $branchId, string $year)
    {
        $this->branchId = $branchId;
        $this->year     = $year;
    }

    public function title(): string { return 'Rekap Penjualan'; }

    public function array(): array
    {
        $rows = [['Bulan', 'Jumlah Transaksi', 'Total Penjualan (Rp)']];
        for ($m = 1; $m <= 12; $m++) {
            $count = Order::where('branch_id', $this->branchId)->where('payment_status', 'paid')->whereYear('created_at', $this->year)->whereMonth('created_at', $m)->count();
            $total = Order::where('branch_id', $this->branchId)->where('payment_status', 'paid')->whereYear('created_at', $this->year)->whereMonth('created_at', $m)->sum('total_amount');
            $rows[] = [\Carbon\Carbon::createFromDate($this->year, $m, 1)->translatedFormat('F Y'), $count, number_format($total, 2, ',', '.')];
        }
        return $rows;
    }
}

// ─── Sheet: Rekap Pinjaman ──────────────────────────────────────────────────
class LoansSummarySheet implements FromArray, WithTitle, ShouldAutoSize
{
    protected int $branchId;
    protected string $year;

    public function __construct(int $branchId, string $year)
    {
        $this->branchId = $branchId;
        $this->year     = $year;
    }

    public function title(): string { return 'Rekap Pinjaman'; }

    public function array(): array
    {
        $rows = [['Status', 'Jumlah', 'Total Nilai (Rp)']];
        foreach (['draft', 'approved', 'active', 'paid_off', 'rejected'] as $s) {
            $count = Loan::where('branch_id', $this->branchId)->where('status', $s)->whereYear('created_at', $this->year)->count();
            $total = Loan::where('branch_id', $this->branchId)->where('status', $s)->whereYear('created_at', $this->year)->sum('amount_approved');
            $rows[] = [ucfirst($s), $count, number_format($total, 2, ',', '.')];
        }
        return $rows;
    }
}

// ─── Sheet: Rekap Hasil Tani ─────────────────────────────────────────────────
class CropsSummarySheet implements FromArray, WithTitle, ShouldAutoSize
{
    protected int $branchId;
    protected string $year;

    public function __construct(int $branchId, string $year)
    {
        $this->branchId = $branchId;
        $this->year     = $year;
    }

    public function title(): string { return 'Rekap Hasil Tani'; }

    public function array(): array
    {
        $rows = [['Komoditas', 'Total Berat (Kg)', 'Total Bayar (Rp)', 'Jumlah Transaksi']];
        $groups = CropAbsorption::where('branch_id', $this->branchId)
            ->where('status', 'paid')
            ->whereYear('created_at', $this->year)
            ->selectRaw('commodity_name, SUM(weight_kg) as total_kg, SUM(total_payout) as total_pay, COUNT(*) as total_count')
            ->groupBy('commodity_name')
            ->get();
        foreach ($groups as $g) {
            $rows[] = [$g->commodity_name, number_format($g->total_kg, 2, ',', '.'), number_format($g->total_pay, 2, ',', '.'), $g->total_count];
        }
        return $rows;
    }
}
