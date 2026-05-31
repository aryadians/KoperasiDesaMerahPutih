<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Member;
use App\Models\CropAbsorption;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CropScaleVerificationTest extends TestCase
{
    use RefreshDatabase;

    private $staffUser;
    private $memberUser;
    private $memberProfile;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup staff
        $this->staffUser = User::factory()->create([
            'role' => 'pengurus',
            'branch_id' => 1,
            'status' => 'active',
        ]);

        // Setup member
        $this->memberUser = User::factory()->create([
            'role' => 'anggota',
            'branch_id' => 1,
            'status' => 'active',
        ]);

        $this->memberProfile = Member::create([
            'user_id' => $this->memberUser->id,
            'nik' => '1234567890123456',
            'nomor_anggota' => 'MBR-001',
            'alamat_desa' => 'Desa Merah Putih',
            'tanggal_bergabung' => '2026-01-01',
            'total_poin' => 0,
            'status_aktif' => true,
        ]);
    }

    /**
     * Test member can submit crop absorption request.
     */
    public function test_member_can_submit_crop_absorption()
    {
        $response = $this->actingAs($this->memberUser)->post(route('member.crops.sell'), [
            'product_name' => 'Padi Premium Lokal',
            'quantity' => 120.50,
            'price_per_unit' => 6000,
        ]);

        $response->assertRedirect(route('member.crops'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('crop_absorptions', [
            'member_id' => $this->memberProfile->id,
            'product_name' => 'Padi Premium Lokal',
            'quantity' => 120.50,
            'price_per_unit' => 6000.00,
            'total_payout' => 723000.00,
            'status' => 'pending',
        ]);
    }

    /**
     * Test staff can verify warehouse reception and upload scale image.
     */
    public function test_staff_can_receive_crop_with_scale_image()
    {
        $crop = CropAbsorption::create([
            'branch_id' => 1,
            'member_id' => $this->memberProfile->id,
            'product_name' => 'Jagung Pipil',
            'quantity' => 80.00,
            'price_per_unit' => 4500.00,
            'total_payout' => 360000.00,
            'status' => 'pending',
            'absorption_date' => now(),
        ]);

        $fakeBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $response = $this->actingAs($this->staffUser)->post(route('staff.crops.update', [$crop->id, 'received']), [
            'scale_image' => $fakeBase64,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $crop->refresh();
        $this->assertEquals('received', $crop->status);
        $this->assertEquals($fakeBase64, $crop->scale_image);
    }

    /**
     * Test cross-branch crop verifications are blocked (BOLA).
     */
    public function test_cross_branch_crop_action_is_forbidden()
    {
        $otherBranchCrop = CropAbsorption::create([
            'branch_id' => 2, // different branch
            'member_id' => $this->memberProfile->id,
            'product_name' => 'Kedelai Lokal',
            'quantity' => 50.00,
            'price_per_unit' => 10000.00,
            'total_payout' => 500000.00,
            'status' => 'pending',
            'absorption_date' => now(),
        ]);

        $response = $this->actingAs($this->staffUser)->post(route('staff.crops.update', [$otherBranchCrop->id, 'received']), [
            'scale_image' => 'someimage',
        ]);

        $response->assertStatus(403);
    }
}
