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
            $member = Member::find($memberId);
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

            return Loan::create([
                'member_id' => $memberId,
                'loan_code' => $loanCode,
                'amount_requested' => $amountRequested,
                'amount_approved' => 0.00,
                'interest_rate' => $interestRate,
                'tenor_months' => $tenorMonths,
                'status' => 'draft',
            ]);
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

            $payment = LoanPayment::create([
                'loan_id' => $loanId,
                'amount_paid' => $amountPaid,
                'penalty' => $penalty,
                'installment_number' => $installmentNumber,
                'payment_date' => Carbon::now(),
            ]);

            // Check if loan is paid off (optional check: if installment_number equals tenor_months or payments cover everything)
            // For simplicity, if we reached the final installment or total paid matches total expected, we mark it paid_off.
            $totalPrincipalApproved = $loan->amount_approved;
            $interestMultiplier = 1 + ($loan->interest_rate / 100);
            $totalExpected = $totalPrincipalApproved * $interestMultiplier;

            $totalPaid = LoanPayment::where('loan_id', $loanId)->sum('amount_paid');

            if ($totalPaid >= $totalExpected || $installmentNumber >= $loan->tenor_months) {
                $loan->status = 'paid_off';
                $loan->save();
            }

            return $payment;
        });
    }
}
