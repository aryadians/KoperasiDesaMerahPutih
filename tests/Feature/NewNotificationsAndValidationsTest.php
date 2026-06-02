<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Member;
use App\Models\MemberSaving;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\CropAbsorption;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NewNotificationsAndValidationsTest extends TestCase
{
    use RefreshDatabase;

    private $memberUser;
    private $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->memberUser = User::factory()->create([
            'role' => 'anggota',
            'branch_id' => 1,
            'status' => 'active',
        ]);

        $this->member = Member::create([
            'user_id' => $this->memberUser->id,
            'nik' => '1234567890123456',
            'nomor_anggota' => 'MBR-777',
            'alamat_desa' => 'Desa Merah Putih',
            'tanggal_bergabung' => '2026-01-01',
            'total_poin' => 0,
            'status_aktif' => true,
            'no_hp' => '081234567890'
        ]);
    }

    public function test_loan_payment_validations()
    {
        $loan = Loan::create([
            'branch_id' => 1,
            'member_id' => $this->member->id,
            'loan_code' => 'LN-TEST-PAY',
            'amount_requested' => 1200000.00,
            'amount_approved' => 1200000.00,
            'interest_rate' => 5.00,
            'tenor_months' => 12,
            'status' => 'active',
        ]);

        $loanService = resolve(\App\Services\LoanService::class);

        // Pay out of order (e.g. installment 2 instead of 1) -> should throw exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Urutan angsuran tidak valid');
        
        $loanService->recordPayment($loan->id, 105000, 0.00, 2);
    }

    public function test_loan_payment_overpay_validation()
    {
        $loan = Loan::create([
            'branch_id' => 1,
            'member_id' => $this->member->id,
            'loan_code' => 'LN-TEST-OVERPAY',
            'amount_requested' => 100000.00,
            'amount_approved' => 100000.00,
            'interest_rate' => 5.00,
            'tenor_months' => 1,
            'status' => 'active',
        ]);

        $loanService = resolve(\App\Services\LoanService::class);

        // Total expected = 100000 * 1.05 = 105000
        // Try to pay 106000 -> should throw exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('melebihi sisa tagihan');
        
        $loanService->recordPayment($loan->id, 106000.00, 0.00, 1);
    }

    public function test_database_notifications_are_created()
    {
        $savingsService = resolve(\App\Services\SavingsService::class);

        // Record a saving deposit
        $savingsService->recordSaving($this->member->id, 'sukarela', 50000.00, 'Test Deposit');

        // Check if database notification was created
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->memberUser->id,
            'type' => 'saving',
            'title' => '💰 Setoran Tabungan Diterima',
        ]);
    }
}
