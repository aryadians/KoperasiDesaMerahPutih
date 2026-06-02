<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Commodity;

class CommoditySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $commodities = [
            ['name' => 'Padi', 'yield_per_m2' => 0.5, 'growth_days' => 100],
            ['name' => 'Jagung', 'yield_per_m2' => 0.8, 'growth_days' => 90],
            ['name' => 'Bawang Merah', 'yield_per_m2' => 1.2, 'growth_days' => 60],
            ['name' => 'Cabai', 'yield_per_m2' => 0.6, 'growth_days' => 85],
        ];

        foreach ($commodities as $commodity) {
            Commodity::updateOrCreate(
                ['name' => $commodity['name']],
                $commodity
            );
        }
    }
}
