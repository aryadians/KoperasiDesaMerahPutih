<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Member;
use App\Models\MemberSaving;
use App\Models\Loan;
use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FinancialPdfReportsTest extends TestCase
{
    use RefreshDatabase;

    private $staffUser;
    private $memberUser;
    private $otherMemberUser;
    private $memberProfile;
    private $otherMemberProfile;
    private $branch;

    protected function setUp(): void
    {
        parent::setUp();

        // Create branch
        $this->branch = Branch::create([
            'name' => 'Desa Makmur',
            'code' => 'MKM',
            'address' => 'Jalan Raya Makmur No. 10',
        ]);

        // Create staff
        $this->staffUser = User::factory()->create([
            'role' => 'pengurus',
            'branch_id' => $this->branch->id,
            'status' => 'active',
        ]);

        // Create member 1
        $this->memberUser = User::factory()->create([
            'role' => 'anggota',
            'branch_id' => $this->branch->id,
            'status' => 'active',
        ]);

        $this->memberProfile = Member::create([
            'user_id' => $this->memberUser->id,
            'nik' => '1234567890123456',
            'nomor_anggota' => 'MBR-001',
            'alamat_desa' => 'Desa Makmur RT 01',
            'tanggal_bergabung' => '2026-01-01',
            'total_poin' => 120,
            'status_aktif' => true,
        ]);

        // Create member 2 (other member)
        $this->otherMemberUser = User::factory()->create([
            'role' => 'anggota',
            'branch_id' => $this->branch->id,
            'status' => 'active',
        ]);

        $this->otherMemberProfile = Member::create([
            'user_id' => $this->otherMemberUser->id,
            'nik' => '9876543210987654',
            'nomor_anggota' => 'MBR-002',
            'alamat_desa' => 'Desa Makmur RT 02',
            'tanggal_bergabung' => '2026-01-05',
            'total_poin' => 50,
            'status_aktif' => true,
        ]);
    }

    /**
     * Test guest cannot access PDF downloads.
     */
    public function test_guest_is_redirected_when_accessing_pdfs()
    {
        $response = $this->get(route('member.savings.pdf'));
        $response->assertStatus(302); // Redirect to login
        
        $response2 = $this->get(route('staff.analytics.rat-pdf'));
        $response2->assertStatus(302);
    }

    /**
     * Test member can download their savings mutation history PDF.
     */
    public function test_member_can_download_savings_pdf()
    {
        // Add some mock savings mutations
        MemberSaving::create([
            'member_id' => $this->memberProfile->id,
            'type' => 'pokok',
            'amount' => 100000,
            'transaction_date' => now(),
            'notes' => 'Simpanan Pokok awal',
        ]);

        MemberSaving::create([
            'member_id' => $this->memberProfile->id,
            'type' => 'sukarela',
            'amount' => 250000,
            'transaction_date' => now(),
            'notes' => 'Setoran Sukarela',
        ]);

        $response = $this->actingAs($this->memberUser)->get(route('member.savings.pdf'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertTrue(str_contains($response->headers->get('Content-Disposition'), 'mutasi_simpanan_MBR-001.pdf'));
    }

    /**
     * Test member can download their own loan receipt and amortization PDF.
     */
    public function test_member_can_download_own_loan_pdf()
    {
        // Create active loan for member 1
        $loan = Loan::create([
            'branch_id' => $this->branch->id,
            'member_id' => $this->memberProfile->id,
            'loan_code' => 'LN-TEST-001',
            'amount_requested' => 1000000.00,
            'amount_approved' => 1000000.00,
            'interest_rate' => 5.00,
            'tenor_months' => 6,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->memberUser)->get(route('member.loans.pdf', $loan->id));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertTrue(str_contains($response->headers->get('Content-Disposition'), 'slip_pinjaman_LN-TEST-001.pdf'));
    }

    /**
     * Test member is forbidden or gets 404 when downloading another member's loan receipt (Security Boundary).
     */
    public function test_member_cannot_download_other_members_loan_pdf()
    {
        // Create loan for member 1
        $loan = Loan::create([
            'branch_id' => $this->branch->id,
            'member_id' => $this->memberProfile->id,
            'loan_code' => 'LN-PRIV-999',
            'amount_requested' => 2000000.00,
            'amount_approved' => 2000000.00,
            'interest_rate' => 5.00,
            'tenor_months' => 12,
            'status' => 'active',
        ]);

        // Attempt download using member 2 (other member user)
        $response = $this->actingAs($this->otherMemberUser)->get(route('member.loans.pdf', $loan->id));

        $response->assertStatus(404); // Returns 404 because Model::findOrFail fails to match the query where('member_id', $otherMemberProfile->id)
    }

    /**
     * Test staff can download the branch's pertanggungjawaban RAT report PDF.
     */
    public function test_staff_can_download_rat_report_pdf()
    {
        $response = $this->actingAs($this->staffUser)->get(route('staff.analytics.rat-pdf'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertTrue(str_contains($response->headers->get('Content-Disposition'), 'laporan_rat_desa_makmur.pdf'));
    }
}
