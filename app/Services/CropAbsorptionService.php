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
            $member = Member::with('user')->find($memberId);
            if (!$member) {
                throw new Exception("Anggota tidak ditemukan.");
            }

            if (!$member->status_aktif) {
                throw new Exception("Anggota tidak aktif, tidak dapat mengajukan penyerapan hasil tani.");
            }

            $totalPayout = $quantity * $pricePerUnit;

            $absorption = CropAbsorption::create([
                'branch_id' => $member->user->branch_id,
                'member_id' => $memberId,
                'product_name' => $productName,
                'quantity' => $quantity,
                'price_per_unit' => $pricePerUnit,
                'total_payout' => $totalPayout,
                'status' => 'pending',
                'absorption_date' => Carbon::now(),
            ]);

            $notificationService = resolve(\App\Services\NotificationService::class);
            $notificationService->createNotification(
                $member->user_id,
                '🌾 Penawaran Panen Diajukan',
                "Penawaran hasil tani {$productName} sebesar {$quantity} kg dengan nilai Rp " . number_format($totalPayout, 0, ',', '.') . " telah diajukan.",
                'crop',
                $absorption->id
            );

            return $absorption;
        });
    }

    /**
     * Update crop absorption status.
     *
     * @param int $absorptionId
     * @param string $status 'pending'|'received'|'paid'
     * @param string|null $scaleImage
     * @return CropAbsorption
     * @throws Exception
     */
    public function updateStatus(int $absorptionId, string $status, ?string $scaleImage = null): CropAbsorption
    {
        if (!in_array($status, ['pending', 'received', 'paid'])) {
            throw new Exception("Status penyerapan tidak valid.");
        }

        return DB::transaction(function () use ($absorptionId, $status, $scaleImage) {
            $absorption = CropAbsorption::where('id', $absorptionId)->lockForUpdate()->first();
            if (!$absorption) {
                throw new Exception("Data penyerapan hasil tani tidak ditemukan.");
            }

            $oldStatus = $absorption->status;
            $absorption->status = $status;

            if ($scaleImage) {
                $absorption->scale_image = $scaleImage;
            }

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
                    
                    // Deduct up to the monthly tagihan, total panen payout, or the remaining debt of active loan
                    $totalPrincipalApproved = $activeLoan->amount_approved;
                    $totalExpected = $totalPrincipalApproved * $interestMultiplier;
                    $totalPaidSoFar = \App\Models\LoanPayment::where('loan_id', $activeLoan->id)->sum('amount_paid') ?? 0.00;
                    $remainingDebt = max(0.00, $totalExpected - $totalPaidSoFar);

                    if ($remainingDebt > 0.01) {
                        $deduction = min($tagihanMonthly, $absorption->total_payout, $remainingDebt);
                    } else {
                        $deduction = 0.00;
                    }
                    $netPayout = $absorption->total_payout - $deduction;

                    $absorption->deducted_loan_payment = $deduction;
                    $absorption->net_payout = $netPayout;
                    
                    if ($deduction > 0) {
                        $absorption->notes = "Potongan otomatis Rp " . number_format($deduction, 0, ',', '.') . " untuk cicilan pinjaman ke-{$nextInstallmentNum} ({$activeLoan->loan_code}).";
                        
                        // Record the loan payment
                        $loanService = resolve(\App\Services\LoanService::class);
                        $loanService->recordPayment($activeLoan->id, $deduction, 0.00, $nextInstallmentNum);
                    } else {
                        $absorption->notes = "Tidak ada potongan cicilan pinjaman karena pinjaman hampir lunas atau sisa tagihan Rp 0.";
                    }
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

                $absorption->load('member.user');
                $notificationService = resolve(\App\Services\NotificationService::class);
                $notificationService->createNotification(
                    $absorption->member->user_id,
                    $smsTitle,
                    $smsMessage,
                    'crop',
                    $absorption->id
                );
            } elseif ($status === 'received' && $oldStatus !== 'received') {
                $absorption->load('member.user');
                $notificationService = resolve(\App\Services\NotificationService::class);
                $notificationService->createNotification(
                    $absorption->member->user_id,
                    '🌾 Hasil Panen Diterima Koperasi',
                    "Hasil tani {$absorption->product_name} Anda telah diterima di timbangan gerai koperasi. Menunggu proses pencairan pembayaran.",
                    'crop',
                    $absorption->id
                );
            } else {
                $absorption->save();
            }

            return $absorption;
        });
    }
}
