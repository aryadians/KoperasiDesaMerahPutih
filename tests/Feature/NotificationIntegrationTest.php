<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Member;
use App\Models\MemberSaving;
use App\Models\SystemConfig;
use App\Models\Loan;
use App\Models\CropAbsorption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;

class NotificationIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private $staffUser;
    private $memberUser;
    private $memberProfile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->staffUser = User::factory()->create([
            'role' => 'pengurus',
            'branch_id' => 1,
            'status' => 'active',
        ]);

        $this->memberUser = User::factory()->create([
            'name' => 'Budi Setiadi',
            'role' => 'anggota',
            'branch_id' => 1,
            'status' => 'active',
        ]);

        $this->memberProfile = Member::create([
            'user_id' => $this->memberUser->id,
            'nik' => '1234567890123456',
            'nomor_anggota' => 'MBR-999',
            'alamat_desa' => 'Desa Makmur',
            'tanggal_bergabung' => '2026-01-01',
            'total_poin' => 0,
            'status_aktif' => true,
            'no_hp' => '081234567890'
        ]);
    }

    /**
     * Test notification sending when gateway is disabled.
     */
    public function test_notification_when_gateway_is_disabled()
    {
        Config::set('services.fonnte.enabled', false);
        Http::fake();

        $service = resolve(\App\Services\NotificationService::class);
        $result = $service->sendMemberNotification($this->memberProfile, 'Test Title', 'Test Message Content');

        // It should return false or log success but not call the HTTP client
        $this->assertFalse($result);
        Http::assertNothingSent();

        // Check that session has the sms_notification anyway (for browser popup)
        $this->assertTrue(session()->has('sms_notification'));
        $this->assertEquals('Test Title', session('sms_notification.title'));
        $this->assertEquals('Test Message Content', session('sms_notification.message'));
    }

    /**
     * Test notification sending when gateway is enabled.
     */
    public function test_notification_when_gateway_is_enabled()
    {
        Config::set('services.fonnte.enabled', true);
        Config::set('services.fonnte.token', 'test-api-token');
        Config::set('services.fonnte.url', 'https://api.fonnte.com/send');

        Http::fake([
            'https://api.fonnte.com/send' => Http::response([
                'status' => true,
                'detail' => 'Message sent successfully'
            ], 200)
        ]);

        $service = resolve(\App\Services\NotificationService::class);
        $result = $service->sendMemberNotification($this->memberProfile, 'Test Title', 'Test Message Content');

        $this->assertTrue($result);

        // Verify HTTP request
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.fonnte.com/send' &&
                $request['target'] === '6281234567890' &&
                str_contains($request['message'], 'Test Message Content') &&
                $request->hasHeader('Authorization', 'test-api-token');
        });

        // Verify session flash is still present
        $this->assertTrue(session()->has('sms_notification'));
    }

    /**
     * Test autodebet sends WhatsApp notification on success.
     */
    public function test_autodebet_triggers_notification()
    {
        Config::set('services.fonnte.enabled', true);
        Config::set('services.fonnte.token', 'test-api-token');
        Http::fake([
            'https://api.fonnte.com/*' => Http::response(['status' => true], 200)
        ]);

        SystemConfig::updateOrCreate(['key' => 'IURAN_WAJIB_NOMINAL'], ['value' => '50000']);
        MemberSaving::create([
            'member_id' => $this->memberProfile->id,
            'type' => 'sukarela',
            'amount' => 100000.00,
            'transaction_date' => now(),
            'notes' => 'Awal',
        ]);

        // Run artisan autodebet
        Artisan::call('kdkmp:run-autodebet');

        // Assert HTTP request was sent for Budi
        Http::assertSent(function ($request) {
            return $request['target'] === '6281234567890' &&
                str_contains($request['message'], 'Autodebet iuran wajib bulanan sebesar Rp 50.000');
        });
    }

    /**
     * Test crop weighing payment triggers notification.
     */
    public function test_crop_weighing_triggers_notification()
    {
        Config::set('services.fonnte.enabled', true);
        Config::set('services.fonnte.token', 'test-api-token');
        Http::fake([
            'https://api.fonnte.com/*' => Http::response(['status' => true], 200)
        ]);

        // Submit crop
        $cropService = resolve(\App\Services\CropAbsorptionService::class);
        $absorption = $cropService->submitAbsorption($this->memberProfile->id, 'Padi Pandan Wangi', 100, 12000);

        // Update status to paid (which triggers payment and notification)
        $cropService->updateStatus($absorption->id, 'paid');

        // Assert WhatsApp sent detailing payout
        Http::assertSent(function ($request) {
            return $request['target'] === '6281234567890' &&
                str_contains($request['message'], 'Padi Pandan Wangi') &&
                str_contains($request['message'], 'Rp 1.200.000');
        });
    }

    /**
     * Test loan approval triggers notification.
     */
    public function test_loan_approval_triggers_notification()
    {
        Config::set('services.fonnte.enabled', true);
        Config::set('services.fonnte.token', 'test-api-token');
        Http::fake([
            'https://api.fonnte.com/*' => Http::response(['status' => true], 200)
        ]);

        $loan = Loan::create([
            'branch_id' => 1,
            'member_id' => $this->memberProfile->id,
            'loan_code' => 'L-TEST-001',
            'amount_requested' => 5000000.00,
            'amount_approved' => 0.00,
            'interest_rate' => 5.00,
            'tenor_months' => 12,
            'status' => 'draft',
            'application_date' => now(),
        ]);

        $loanService = resolve(\App\Services\LoanService::class);
        $loanService->updateStatus($loan->id, 'approved', 5000000.00);

        // Assert WhatsApp sent for loan approval
        Http::assertSent(function ($request) {
            return $request['target'] === '6281234567890' &&
                str_contains($request['message'], 'L-TEST-001') &&
                str_contains($request['message'], 'disetujui sebesar Rp 5.000.000');
        });
    }
}
