<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\SHUService;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SHUServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculate_shu_distribution_correctly()
    {
        // 1. Setup mock data
        $user1 = User::factory()->create();
        $member1 = Member::create([
            'user_id' => $user1->id, 
            'nomor_anggota' => 'A001', 
            'nik' => '1234567890123456', 
            'alamat_desa' => 'Desa A',
            'telepon' => '08123',
            'tanggal_bergabung' => '2026-01-01',
            'total_poin' => 100, 
            'status_aktif' => true
        ]);

        $user2 = User::factory()->create();
        $member2 = Member::create([
            'user_id' => $user2->id, 
            'nomor_anggota' => 'A002', 
            'nik' => '1234567890123457', 
            'alamat_desa' => 'Desa B',
            'telepon' => '08124',
            'tanggal_bergabung' => '2026-01-01',
            'total_poin' => 300, 
            'status_aktif' => true
        ]);

        $shuService = app(SHUService::class);
        $totalPool = 1000000; // 1 Million

        // 2. Execute
        $distribution = $shuService->calculateSHUDistribution($totalPool);

        // 3. Assert
        $this->assertCount(2, $distribution);
        
        // Poin 1: 100, Poin 2: 300. Total 400.
        // Member 1: (100/400) * 1M = 250,000
        // Member 2: (300/400) * 1M = 750,000
        
        $this->assertEquals(250000, $distribution[0]['share']);
        $this->assertEquals(750000, $distribution[1]['share']);
    }

    public function test_distribute_shu_executes_and_resets_points()
    {
        // 1. Setup
        $user1 = User::factory()->create();
        $member1 = Member::create([
            'user_id' => $user1->id, 'nomor_anggota' => 'A001', 'nik' => '111', 'alamat_desa' => 'A',
            'telepon' => '1', 'tanggal_bergabung' => '2026-01-01', 'total_poin' => 100, 'status_aktif' => true
        ]);
        
        $shuService = app(SHUService::class);
        $totalPool = 100000;

        // 2. Execute
        $result = $shuService->distributeSHU($totalPool);

        // 3. Assert
        $this->assertEquals(100000, $result['total_distributed']);
        
        $member1->refresh();
        $this->assertEquals(0, $member1->total_poin);
        
        // Check if savings were recorded
        $this->assertDatabaseHas('member_savings', [
            'member_id' => $member1->id,
            'amount' => 100000,
            'type' => 'sukarela'
        ]);
    }
}
