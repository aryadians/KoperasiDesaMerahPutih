<?php

namespace App\Services;

use App\Models\MemberLand;
use Carbon\Carbon;

class AgroService
{
    /**
     * Calculate harvest forecast based on land size and commodity.
     */
    public function getHarvestForecast(MemberLand $land)
    {
        $yieldPerM2 = 0;
        $growthDays = 0;

        // Simplified yield and growth data
        switch (strtolower($land->commodity_type)) {
            case 'padi':
                $yieldPerM2 = 0.5; // 0.5kg per m2
                $growthDays = 100;
                break;
            case 'jagung':
                $yieldPerM2 = 0.8;
                $growthDays = 90;
                break;
            default:
                $yieldPerM2 = 0.3;
                $growthDays = 120;
        }

        $estimatedYield = $land->area_m2 * $yieldPerM2;
        $harvestDate = $land->last_planting_date 
            ? Carbon::parse($land->last_planting_date)->addDays($growthDays) 
            : null;

        return [
            'estimated_yield_kg' => $estimatedYield,
            'harvest_date_forecast' => $harvestDate ? $harvestDate->format('d M Y') : 'N/A',
            'days_until_harvest' => $harvestDate ? now()->diffInDays($harvestDate, false) : 'N/A',
        ];
    }
}
