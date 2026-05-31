<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Member;
use App\Models\MemberSaving;
use App\Models\SystemConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

class FinancialAutodebetTest extends TestCase
{
    use RefreshDatabase;

    private $staffUser;
    private $memberUser;
    private $memberProfile;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup staff user
        $this->staffUser = User::factory()->create([
            'role' => 'pengurus',
            'branch_id' => 1,
            'status' => 'active',
        ]);

        // Setup member user
        $this->memberUser = User::factory()->create([
            'role' => 'anggota',
            'branch_id' => 1,
            'status' => 'active',
        ]);

        $this->memberProfile = Member::create([
            'user_id' => $this->memberUser->id,
            'nik' => '1234567890123456',
            'nomor_anggota' => 'MBR-001',
            'alamat_desa' => 'Desa Merah',
            'tanggal_bergabung' => '2026-01-01',
            'total_poin' => 0,
            'status_aktif' => true,
        ]);
    }

    /**
     * Test configuration updates.
     */
    public function test_staff_can_update_iuran_configs()
    {
        $response = $this->actingAs($this->staffUser)->post(route('staff.config.update'), [
            'app_name' => 'Koperasi Baru',
            'app_env' => 'local',
            'app_debug' => 'true',
            'session_driver' => 'file',
            'session_lifetime' => 120,
            'iuran_wajib_nominal' => 60000,
            'iuran_pokok_nominal' => 120000,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('system_configs', [
            'key' => 'IURAN_WAJIB_NOMINAL',
            'value' => '60000',
        ]);

        $this->assertDatabaseHas('system_configs', [
            'key' => 'IURAN_POKOK_NOMINAL',
            'value' => '120000',
        ]);
    }

    /**
     * Test manual autodebet execution via Controller.
     */
    public function test_manual_autodebet_execution()
    {
        // Set up configurations
        SystemConfig::updateOrCreate(['key' => 'IURAN_WAJIB_NOMINAL'], ['value' => '60000']);

        // Give the member some voluntary savings balance (sukarela)
        MemberSaving::create([
            'member_id' => $this->memberProfile->id,
            'type' => 'sukarela',
            'amount' => 100000.00,
            'transaction_date' => now(),
            'notes' => 'Awal',
        ]);

        $response = $this->actingAs($this->staffUser)->post(route('staff.autodebet'));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check that Rp 60.000 was debited from sukarela
        $this->assertDatabaseHas('member_savings', [
            'member_id' => $this->memberProfile->id,
            'type' => 'sukarela',
            'amount' => -60000.00,
            'notes' => 'Autodebet bulanan untuk Simpanan Wajib',
        ]);

        // Check that Rp 60.000 was credited to wajib
        $this->assertDatabaseHas('member_savings', [
            'member_id' => $this->memberProfile->id,
            'type' => 'wajib',
            'amount' => 60000.00,
            'notes' => 'Setoran Simpanan Wajib via Autodebet Sukarela',
        ]);
    }

    /**
     * Test autodebet execution via Artisan Command.
     */
    public function test_artisan_command_autodebet()
    {
        // Set up configurations
        SystemConfig::updateOrCreate(['key' => 'IURAN_WAJIB_NOMINAL'], ['value' => '45000']);

        // Give the member some voluntary savings balance
        MemberSaving::create([
            'member_id' => $this->memberProfile->id,
            'type' => 'sukarela',
            'amount' => 50000.00,
            'transaction_date' => now(),
            'notes' => 'Awal',
        ]);

        // Run Artisan command
        Artisan::call('kdkmp:run-autodebet');

        // Check database
        $this->assertDatabaseHas('member_savings', [
            'member_id' => $this->memberProfile->id,
            'type' => 'sukarela',
            'amount' => -45000.00,
        ]);

        $this->assertDatabaseHas('member_savings', [
            'member_id' => $this->memberProfile->id,
            'type' => 'wajib',
            'amount' => 45000.00,
        ]);
    }

    /**
     * Test autodebet failure when voluntary balance is insufficient.
     */
    public function test_autodebet_skips_when_insufficient_balance()
    {
        SystemConfig::updateOrCreate(['key' => 'IURAN_WAJIB_NOMINAL'], ['value' => '50000']);

        // Give the member only Rp 30.000 (which is less than Rp 50.000)
        MemberSaving::create([
            'member_id' => $this->memberProfile->id,
            'type' => 'sukarela',
            'amount' => 30000.00,
            'transaction_date' => now(),
            'notes' => 'Awal',
        ]);

        Artisan::call('kdkmp:run-autodebet');

        // Voluntary savings should still be 30000 (no debits occurred)
        $this->assertDatabaseMissing('member_savings', [
            'member_id' => $this->memberProfile->id,
            'type' => 'sukarela',
            'amount' => -50000.00,
        ]);

        $this->assertDatabaseMissing('member_savings', [
            'member_id' => $this->memberProfile->id,
            'type' => 'wajib',
            'amount' => 50000.00,
        ]);
    }
}
