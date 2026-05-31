<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class MemberManagementTest extends TestCase
{
    use RefreshDatabase;

    private $staffUser;
    private $otherBranchStaff;

    protected function setUp(): void
    {
        parent::setUp();

        // branch_id 1 is seeded automatically in migrations, but let's make sure our users are created
        $this->staffUser = User::factory()->create([
            'role' => 'pengurus',
            'branch_id' => 1,
            'status' => 'active',
        ]);

        $this->otherBranchStaff = User::factory()->create([
            'role' => 'pengurus',
            'branch_id' => 2,
            'status' => 'active',
        ]);
    }

    /**
     * Test view members list is only accessible to authenticated staff.
     */
    public function test_members_list_is_accessible_to_staff()
    {
        $response = $this->get(route('staff.members'));
        $response->assertRedirect(route('login'));

        $response = $this->actingAs($this->staffUser)->get(route('staff.members'));
        $response->assertStatus(200);
        $response->assertSee('Manajemen Anggota');
    }

    /**
     * Test views show only users from the same branch.
     */
    public function test_members_list_displays_only_same_branch_users()
    {
        $sameBranchUser = User::factory()->create([
            'name' => 'Same Branch User',
            'branch_id' => 1,
        ]);

        $diffBranchUser = User::factory()->create([
            'name' => 'Different Branch User',
            'branch_id' => 2,
        ]);

        $response = $this->actingAs($this->staffUser)->get(route('staff.members'));
        $response->assertStatus(200);
        $response->assertSee('Same Branch User');
        $response->assertDontSee('Different Branch User');
    }

    /**
     * Test creation of new member (anggota role).
     */
    public function test_staff_can_create_member()
    {
        $response = $this->actingAs($this->staffUser)->post(route('staff.members.store'), [
            'name' => 'New Anggota Member',
            'email' => 'anggota@example.com',
            'password' => 'password123',
            'role' => 'anggota',
            'status' => 'active',
            'nik' => '1234567890123456',
            'alamat_desa' => 'Desa Merah RT 01 RW 02',
            'ktp_image' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            'no_hp' => '081234567890',
        ]);

        $response->assertRedirect(route('staff.members'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'New Anggota Member',
            'email' => 'anggota@example.com',
            'role' => 'anggota',
            'branch_id' => 1,
        ]);

        $user = User::where('email', 'anggota@example.com')->first();

        $this->assertDatabaseHas('members', [
            'user_id' => $user->id,
            'nik' => '1234567890123456',
            'alamat_desa' => 'Desa Merah RT 01 RW 02',
            'ktp_image' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
        ]);
    }

    /**
     * Test creation validation rules.
     */
    public function test_staff_create_member_validation()
    {
        // NIK required if role is anggota
        $response = $this->actingAs($this->staffUser)->post(route('staff.members.store'), [
            'name' => 'Invalid Anggota',
            'email' => 'invalid@example.com',
            'password' => 'password123',
            'role' => 'anggota',
            'status' => 'active',
            // NIK and Alamat Desa omitted
        ]);

        $response->assertSessionHasErrors(['nik', 'alamat_desa']);
    }

    /**
     * Test staff can update member details.
     */
    public function test_staff_can_update_member()
    {
        $memberUser = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'role' => 'anggota',
            'branch_id' => 1,
        ]);

        Member::create([
            'user_id' => $memberUser->id,
            'nik' => '1111111111111111',
            'nomor_anggota' => 'MBR-001',
            'alamat_desa' => 'Old Address',
            'tanggal_bergabung' => '2026-01-01',
            'total_poin' => 10,
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($this->staffUser)->post(route('staff.members.update', $memberUser->id), [
            'name' => 'New Name',
            'email' => 'new@example.com',
            'role' => 'anggota',
            'status' => 'inactive',
            'nik' => '2222222222222222',
            'alamat_desa' => 'New Address',
            'no_hp' => '081234567890',
        ]);

        $response->assertRedirect(route('staff.members'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $memberUser->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
            'status' => 'inactive',
        ]);

        $this->assertDatabaseHas('members', [
            'user_id' => $memberUser->id,
            'nik' => '2222222222222222',
            'alamat_desa' => 'New Address',
            'status_aktif' => false,
        ]);
    }

    /**
     * Test cross-branch modification is prohibited (BOLA check).
     */
    public function test_cross_branch_update_is_forbidden()
    {
        $otherBranchUser = User::factory()->create([
            'name' => 'Other Branch User',
            'email' => 'other@example.com',
            'role' => 'anggota',
            'branch_id' => 2,
        ]);

        Member::create([
            'user_id' => $otherBranchUser->id,
            'nik' => '1111111111111111',
            'nomor_anggota' => 'MBR-002',
            'alamat_desa' => 'Other Address',
            'tanggal_bergabung' => '2026-01-01',
            'total_poin' => 0,
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($this->staffUser)->post(route('staff.members.update', $otherBranchUser->id), [
            'name' => 'Hacked Name',
            'email' => 'hacked@example.com',
            'role' => 'anggota',
            'status' => 'active',
            'nik' => '1111111111111111',
            'alamat_desa' => 'Other Address',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test staff can delete user.
     */
    public function test_staff_can_delete_member()
    {
        $memberUser = User::factory()->create([
            'role' => 'anggota',
            'branch_id' => 1,
        ]);

        $member = Member::create([
            'user_id' => $memberUser->id,
            'nik' => '1111111111111111',
            'nomor_anggota' => 'MBR-003',
            'alamat_desa' => 'Address',
            'tanggal_bergabung' => '2026-01-01',
            'total_poin' => 0,
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($this->staffUser)->post(route('staff.members.delete', $memberUser->id));
        $response->assertRedirect(route('staff.members'));
        $response->assertSessionHas('success');

        // Since soft deletes are used
        $this->assertSoftDeleted('users', ['id' => $memberUser->id]);
        $this->assertSoftDeleted('members', ['id' => $member->id]);
    }

    /**
     * Test user cannot delete themselves.
     */
    public function test_user_cannot_delete_themselves()
    {
        $response = $this->actingAs($this->staffUser)->post(route('staff.members.delete', $this->staffUser->id));
        $response->assertSessionHasErrors(['error']);
        $this->assertDatabaseHas('users', ['id' => $this->staffUser->id, 'deleted_at' => null]);
    }

    /**
     * Test bulk delete functionality.
     */
    public function test_bulk_delete_members()
    {
        $user1 = User::factory()->create(['branch_id' => 1]);
        $user2 = User::factory()->create(['branch_id' => 1]);
        $otherBranchUser = User::factory()->create(['branch_id' => 2]);

        $response = $this->actingAs($this->staffUser)->post(route('staff.members.bulk-delete'), [
            'ids' => implode(',', [$user1->id, $user2->id, $otherBranchUser->id]),
        ]);

        $response->assertRedirect(route('staff.members'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('users', ['id' => $user1->id]);
        $this->assertSoftDeleted('users', ['id' => $user2->id]);
        
        // Other branch user must NOT be deleted (BOLA / Branch scoping protection)
        $this->assertDatabaseHas('users', ['id' => $otherBranchUser->id, 'deleted_at' => null]);
    }

    /**
     * Test CSV export.
     */
    public function test_export_members_csv()
    {
        $user = User::factory()->create(['name' => 'CSV Member', 'branch_id' => 1]);

        $response = $this->actingAs($this->staffUser)->get(route('staff.members.export'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertSee('CSV Member');
    }
}
