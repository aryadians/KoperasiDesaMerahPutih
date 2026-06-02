<?php

namespace App\Services;

use App\Models\MemberLand;
use App\Models\Commodity;
use Carbon\Carbon;

class AgroService
{
    /**
     * Calculate harvest forecast based on land size and commodity.
     */
    public function getHarvestForecast(MemberLand $land)
    {
        if ($land->status === 'idle') {
            return [
                'estimated_yield_kg' => 0,
                'harvest_date_forecast' => 'Lahan Kosong',
                'days_until_harvest' => '-',
                'status_message' => 'Belum ada jadwal tanam'
            ];
        }

        $commodity = Commodity::where('name', $land->commodity_type)->first();

        // Fallback defaults if commodity not found in DB
        $yieldPerM2 = $commodity ? $commodity->yield_per_m2 : 0.3;
        $growthDays = $commodity ? $commodity->growth_days : 120;

        $estimatedYield = $land->area_m2 * $yieldPerM2;
        $harvestDate = $land->last_planting_date 
            ? Carbon::parse($land->last_planting_date)->addDays($growthDays) 
            : null;

        $daysUntil = $harvestDate ? now()->diffInDays($harvestDate, false) : null;
        $statusMsg = 'Menunggu Panen';

        if ($daysUntil !== null) {
            if ($daysUntil < 0) {
                // Past harvest date
                $daysUntil = 0;
                $statusMsg = 'Siap Panen / Masa Panen Terlewat';
                if ($land->status !== 'ready_for_harvest') {
                    $land->status = 'ready_for_harvest';
                    $land->save();
                }
            } elseif ($land->status !== 'planting') {
                $land->status = 'planting';
                $land->save();
            }
        }

        return [
            'estimated_yield_kg' => $estimatedYield,
            'harvest_date_forecast' => $harvestDate ? $harvestDate->format('d M Y') : 'N/A',
            'days_until_harvest' => $daysUntil !== null ? $daysUntil : 'N/A',
            'status_message' => $statusMsg
        ];
    }
}

