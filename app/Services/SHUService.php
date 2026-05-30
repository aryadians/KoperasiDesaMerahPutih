<?php

namespace App\Services;

use App\Models\Member;
use App\Services\SavingsService;
use Illuminate\Support\Facades\DB;
use Exception;

class SHUService
{
    protected $savingsService;

    public function __construct(SavingsService $savingsService)
    {
        $this->savingsService = $savingsService;
    }

    /**
     * Calculate and allocate SHU sharing for all members based on loyalty points.
     *
     * @param float $totalSHUPool
     * @return array Array of ['member_id' => int, 'nomor_anggota' => string, 'name' => string, 'points' => int, 'share' => float]
     * @throws Exception
     */
    public function calculateSHUDistribution(float $totalSHUPool): array
    {
        if ($totalSHUPool <= 0) {
            throw new Exception("Jumlah dana SHU harus lebih besar dari nol.");
        }

        // Get total points of all active members
        $totalPoints = Member::where('status_aktif', true)->sum('total_poin');

        if ($totalPoints <= 0) {
            throw new Exception("Tidak ada poin anggota aktif yang tercatat untuk pembagian SHU.");
        }

        $members = Member::with('user')
            ->where('status_aktif', true)
            ->get();

        $distribution = [];

        foreach ($members as $member) {
            $share = ($member->total_poin / $totalPoints) * $totalSHUPool;
            $distribution[] = [
                'member_id' => $member->id,
                'nomor_anggota' => $member->nomor_anggota,
                'name' => $member->user->name,
                'points' => $member->total_poin,
                'share' => round($share, 2),
            ];
        }

        return $distribution;
    }

    /**
     * Distribute SHU and reset points.
     *
     * @param float $totalSHUPool
     * @return array Summary of distribution
     * @throws Exception
     */
    public function distributeSHU(float $totalSHUPool): array
    {
        return DB::transaction(function () use ($totalSHUPool) {
            $distribution = $this->calculateSHUDistribution($totalSHUPool);
            $totalDistributed = 0;
            $memberCount = count($distribution);

            foreach ($distribution as $item) {
                if ($item['share'] > 0) {
                    // Credit to Simpanan Sukarela
                    $this->savingsService->recordSaving(
                        $item['member_id'],
                        'sukarela',
                        $item['share'],
                        "Penerimaan Dividen SHU Tahunan (Loyalitas: {$item['points']} Poin)"
                    );
                    $totalDistributed += $item['share'];
                }

                // Reset member points to 0 after distribution
                Member::where('id', $item['member_id'])->update(['total_poin' => 0]);
            }

            return [
                'total_distributed' => $totalDistributed,
                'member_count' => $memberCount,
            ];
        });
    }
}
