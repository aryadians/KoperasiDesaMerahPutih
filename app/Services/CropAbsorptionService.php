<?php

namespace App\Services;

use App\Models\CropAbsorption;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class CropAbsorptionService
{
    /**
     * Submit a crop absorption request (farmer sells crop to cooperative).
     *
     * @param int $memberId
     * @param string $productName
     * @param float $quantity
     * @param float $pricePerUnit
     * @return CropAbsorption
     * @throws Exception
     */
    public function submitAbsorption(int $memberId, string $productName, float $quantity, float $pricePerUnit): CropAbsorption
    {
        if ($quantity <= 0 || $pricePerUnit <= 0) {
            throw new Exception("Kuantitas dan harga satuan harus lebih besar dari nol.");
        }

        return DB::transaction(function () use ($memberId, $productName, $quantity, $pricePerUnit) {
            $member = Member::find($memberId);
            if (!$member) {
                throw new Exception("Anggota tidak ditemukan.");
            }

            if (!$member->status_aktif) {
                throw new Exception("Anggota tidak aktif, tidak dapat mengajukan penyerapan hasil tani.");
            }

            $totalPayout = $quantity * $pricePerUnit;

            return CropAbsorption::create([
                'member_id' => $memberId,
                'product_name' => $productName,
                'quantity' => $quantity,
                'price_per_unit' => $pricePerUnit,
                'total_payout' => $totalPayout,
                'status' => 'pending',
                'absorption_date' => Carbon::now(),
            ]);
        });
    }

    /**
     * Update crop absorption status.
     *
     * @param int $absorptionId
     * @param string $status 'pending'|'received'|'paid'
     * @return CropAbsorption
     * @throws Exception
     */
    public function updateStatus(int $absorptionId, string $status): CropAbsorption
    {
        if (!in_array($status, ['pending', 'received', 'paid'])) {
            throw new Exception("Status penyerapan tidak valid.");
        }

        return DB::transaction(function () use ($absorptionId, $status) {
            $absorption = CropAbsorption::where('id', $absorptionId)->lockForUpdate()->first();
            if (!$absorption) {
                throw new Exception("Data penyerapan hasil tani tidak ditemukan.");
            }

            $oldStatus = $absorption->status;
            $absorption->status = $status;

            if ($status === 'paid' && $oldStatus !== 'paid') {
                // Check if member has an active loan to auto-deduct
                $activeLoan = \App\Models\Loan::where('member_id', $absorption->member_id)
                    ->where('status', 'active')
                    ->first();

                $deduction = 0.00;
                $netPayout = $absorption->total_payout;

                if ($activeLoan) {
                    $interestMultiplier = 1 + ($activeLoan->interest_rate / 100);
                    $tagihanMonthly = ($activeLoan->amount_approved * $interestMultiplier) / $activeLoan->tenor_months;
                    
                    $nextInstallmentNum = \App\Models\LoanPayment::where('loan_id', $activeLoan->id)->count() + 1;
                    
                    // Deduct up to the monthly tagihan or the total panen payout
                    $deduction = min($tagihanMonthly, $absorption->total_payout);
                    $netPayout = $absorption->total_payout - $deduction;

                    $absorption->deducted_loan_payment = $deduction;
                    $absorption->net_payout = $netPayout;
                    $absorption->notes = "Potongan otomatis Rp " . number_format($deduction, 0, ',', '.') . " untuk cicilan pinjaman ke-{$nextInstallmentNum} ({$activeLoan->loan_code}).";

                    // Record the loan payment
                    $loanService = resolve(\App\Services\LoanService::class);
                    $loanService->recordPayment($activeLoan->id, $deduction, 0.00, $nextInstallmentNum);
                } else {
                    $absorption->deducted_loan_payment = 0.00;
                    $absorption->net_payout = $absorption->total_payout;
                }

                // Deposit the net payout directly to member's tabungan sukarela
                $savingsService = resolve(\App\Services\SavingsService::class);
                if ($netPayout > 0) {
                    $savingsService->recordSaving(
                        $absorption->member_id,
                        'sukarela',
                        $netPayout,
                        "Hasil bersih penyerapan panen: {$absorption->product_name}"
                    );
                }

                // Save absorption updates
                $absorption->save();

                // Flash WhatsApp Notification Simulation
                $smsTitle = '🌾 Panen Dibayar & Kredit Terpotong';
                $smsMessage = "Hasil panen {$absorption->product_name} Anda telah dilunasi senilai Rp " . number_format($absorption->total_payout, 0, ',', '.') . ". ";
                if ($deduction > 0) {
                    $smsMessage .= "Dipotong Rp " . number_format($deduction, 0, ',', '.') . " untuk angsuran pinjaman {$activeLoan->loan_code}. ";
                }
                $smsMessage .= "Sisa bersih Rp " . number_format($netPayout, 0, ',', '.') . " disetor ke Saldo Sukarela Anda.";

                session()->flash('sms_notification', [
                    'title' => $smsTitle,
                    'message' => $smsMessage
                ]);
            } else {
                $absorption->save();
            }

            return $absorption;
        });
    }
}
