<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class LoanService
{
    /**
     * Submit a loan request.
     *
     * @param int $memberId
     * @param float $amountRequested
     * @param float $interestRate
     * @param int $tenorMonths
     * @return Loan
     * @throws Exception
     */
    public function applyLoan(int $memberId, float $amountRequested, float $interestRate, int $tenorMonths): Loan
    {
        if ($amountRequested <= 0) {
            throw new Exception("Nominal pinjaman harus lebih besar dari nol.");
        }

        if ($tenorMonths <= 0) {
            throw new Exception("Tenor bulan harus minimal 1 bulan.");
        }

        return DB::transaction(function () use ($memberId, $amountRequested, $interestRate, $tenorMonths) {
            $member = Member::with('user')->find($memberId);
            if (!$member) {
                throw new Exception("Anggota tidak ditemukan.");
            }

            if (!$member->status_aktif) {
                throw new Exception("Anggota tidak aktif, tidak dapat mengajukan pinjaman.");
            }

            // Check if member has an active loan already
            $activeLoanExists = Loan::where('member_id', $memberId)
                ->whereIn('status', ['approved', 'active'])
                ->exists();

            if ($activeLoanExists) {
                throw new Exception("Anggota masih memiliki pinjaman aktif yang belum lunas.");
            }

            $loanCode = 'LN-' . strtoupper(uniqid());

            $loan = Loan::create([
                'branch_id' => $member->user->branch_id,
                'member_id' => $memberId,
                'loan_code' => $loanCode,
                'amount_requested' => $amountRequested,
                'amount_approved' => 0.00,
                'interest_rate' => $interestRate,
                'tenor_months' => $tenorMonths,
                'status' => 'draft',
            ]);

            $notificationService = resolve(\App\Services\NotificationService::class);
            $notificationService->createNotification(
                $member->user_id,
                '📝 Pengajuan Pinjaman Diajukan',
                "Pengajuan pinjaman Anda ({$loan->loan_code}) senilai Rp " . number_format($amountRequested, 0, ',', '.') . " sedang diproses.",
                'loan',
                $loan->id
            );

            return $loan;
        });
    }

    /**
     * Update loan status (Approve, Reject, Disburse/Activate, Pay off).
     *
     * @param int $loanId
     * @param string $status
     * @param float|null $amountApproved
     * @return Loan
     * @throws Exception
     */
    public function updateStatus(int $loanId, string $status, ?float $amountApproved = null): Loan
    {
        if (!in_array($status, ['approved', 'rejected', 'active', 'paid_off'])) {
            throw new Exception("Status pinjaman tidak valid.");
        }

        return DB::transaction(function () use ($loanId, $status, $amountApproved) {
            $loan = Loan::where('id', $loanId)->lockForUpdate()->first();
            if (!$loan) {
                throw new Exception("Data pinjaman tidak ditemukan.");
            }

            if ($status === 'approved') {
                $loan->amount_approved = $amountApproved ?? $loan->amount_requested;
            }

            $loan->status = $status;
            $loan->save();

            $loan->load('member.user');
            $notificationService = resolve(\App\Services\NotificationService::class);

            if (in_array($status, ['approved', 'active'])) {
                $title = $status === 'approved' ? '💸 Pengajuan Pinjaman Disetujui' : '💰 Pinjaman Dicairkan';
                $message = $status === 'approved' 
                    ? "Pengajuan pinjaman Anda ({$loan->loan_code}) telah disetujui sebesar Rp " . number_format($loan->amount_approved, 0, ',', '.') . " dengan tenor {$loan->tenor_months} bulan."
                    : "Dana pinjaman Anda ({$loan->loan_code}) sebesar Rp " . number_format($loan->amount_approved, 0, ',', '.') . " telah dicairkan ke rekening Anda. Silakan cek saldo Anda.";

                $notificationService->createNotification($loan->member->user_id, $title, $message, 'loan', $loan->id);
            } elseif ($status === 'rejected') {
                $notificationService->createNotification(
                    $loan->member->user_id,
                    '❌ Pengajuan Pinjaman Ditolak',
                    "Pengajuan pinjaman Anda ({$loan->loan_code}) ditolak oleh pengurus.",
                    'loan',
                    $loan->id
                );
            }

            return $loan;
        });
    }

    /**
     * Record a payment installment for a loan.
     *
     * @param int $loanId
     * @param float $amountPaid
     * @param float $penalty
     * @param int $installmentNumber
     * @return LoanPayment
     * @throws Exception
     */
    public function recordPayment(int $loanId, float $amountPaid, float $penalty, int $installmentNumber): LoanPayment
    {
        if ($amountPaid <= 0) {
            throw new Exception("Nominal pembayaran harus lebih besar dari nol.");
        }

        return DB::transaction(function () use ($loanId, $amountPaid, $penalty, $installmentNumber) {
            $loan = Loan::where('id', $loanId)->lockForUpdate()->first();
            if (!$loan) {
                throw new Exception("Data pinjaman tidak ditemukan.");
            }

            if ($loan->status !== 'active') {
                throw new Exception("Pinjaman tidak aktif atau sudah lunas.");
            }

            // 1. Validate payment sequence: must match count() + 1
            $nextInstallmentNum = LoanPayment::where('loan_id', $loanId)->count() + 1;
            if ($installmentNumber !== $nextInstallmentNum) {
                throw new Exception("Urutan angsuran tidak valid. Angsuran berikutnya yang harus dibayar adalah ke-{$nextInstallmentNum}.");
            }

            // 2. Validate bounds: cannot pay more than the total remaining debt
            $totalPrincipalApproved = $loan->amount_approved;
            $interestMultiplier = 1 + ($loan->interest_rate / 100);
            $totalExpected = $totalPrincipalApproved * $interestMultiplier;

            $totalPaidSoFar = LoanPayment::where('loan_id', $loanId)->sum('amount_paid') ?? 0.00;
            $remainingDebt = max(0.00, $totalExpected - $totalPaidSoFar);

            // Using tiny tolerance for float comparisons (0.01)
            if ($amountPaid > $remainingDebt + 0.01) {
                throw new Exception("Nominal pembayaran Rp " . number_format($amountPaid, 2, ',', '.') . " melebihi sisa tagihan Rp " . number_format($remainingDebt, 2, ',', '.') . ".");
            }

            $payment = LoanPayment::create([
                'loan_id' => $loanId,
                'amount_paid' => $amountPaid,
                'penalty' => $penalty,
                'installment_number' => $installmentNumber,
                'payment_date' => Carbon::now(),
            ]);

            // Phase 10: Generate simulated gateway link for tracking/online verification
            $paymentService = resolve(\App\Services\PaymentService::class);
            $session = $paymentService->createPaymentSession('va', (float) ($amountPaid + $penalty), "PAY-{$loan->loan_code}-{$installmentNumber}");
            
            $payment->payment_gateway_ref = $session['gateway_ref'];
            $payment->payment_url = $session['payment_url'];
            $payment->save();

            // Check if loan is paid off
            $totalPaid = $totalPaidSoFar + $amountPaid;

            if ($totalPaid >= $totalExpected - 0.01 || $installmentNumber >= $loan->tenor_months) {
                $loan->status = 'paid_off';
                $loan->save();
            }

            // 3. Dispatch Notification
            $loan->load('member.user');
            $notificationService = resolve(\App\Services\NotificationService::class);
            $notificationService->createNotification(
                $loan->member->user_id,
                '💰 Angsuran Pinjaman Diterima',
                "Pembayaran angsuran ke-{$installmentNumber} untuk pinjaman {$loan->loan_code} sebesar Rp " . number_format($amountPaid, 0, ',', '.') . " berhasil dicatat.",
                'loan',
                $loan->id
            );

            return $payment;
        });
    }
}
