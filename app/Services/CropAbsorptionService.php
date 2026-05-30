<?php

namespace App\Services;

use App\Models\CropAbsorption;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class CropAbsorptionService
{
    /**
     * Submit a crop absorption request (farmer sells crop to cooperative).
     *
     * @param int $memberId
     * @param string $productName
     * @param float $quantity
     * @param float $pricePerUnit
     * @return CropAbsorption
     * @throws Exception
     */
    public function submitAbsorption(int $memberId, string $productName, float $quantity, float $pricePerUnit): CropAbsorption
    {
        if ($quantity <= 0 || $pricePerUnit <= 0) {
            throw new Exception("Kuantitas dan harga satuan harus lebih besar dari nol.");
        }

        return DB::transaction(function () use ($memberId, $productName, $quantity, $pricePerUnit) {
            $member = Member::find($memberId);
            if (!$member) {
                throw new Exception("Anggota tidak ditemukan.");
            }

            if (!$member->status_aktif) {
                throw new Exception("Anggota tidak aktif, tidak dapat mengajukan penyerapan hasil tani.");
            }

            $totalPayout = $quantity * $pricePerUnit;

            return CropAbsorption::create([
                'member_id' => $memberId,
                'product_name' => $productName,
                'quantity' => $quantity,
                'price_per_unit' => $pricePerUnit,
                'total_payout' => $totalPayout,
                'status' => 'pending',
                'absorption_date' => Carbon::now(),
            ]);
        });
    }

    /**
     * Update crop absorption status.
     *
     * @param int $absorptionId
     * @param string $status 'pending'|'received'|'paid'
     * @return CropAbsorption
     * @throws Exception
     */
    public function updateStatus(int $absorptionId, string $status): CropAbsorption
    {
        if (!in_array($status, ['pending', 'received', 'paid'])) {
            throw new Exception("Status penyerapan tidak valid.");
        }

        return DB::transaction(function () use ($absorptionId, $status) {
            $absorption = CropAbsorption::where('id', $absorptionId)->lockForUpdate()->first();
            if (!$absorption) {
                throw new Exception("Data penyerapan hasil tani tidak ditemukan.");
            }

            $absorption->status = $status;
            $absorption->save();

            // When paid or received, we can optionally register the crop as store inventory
            // or simply mark as received/paid. 
            // In a real system, 'received' or 'paid' would increment the product inventory if a matching product is set up.
            // Let's keep it simple and update the status for auditability.

            return $absorption;
        });
    }
}
