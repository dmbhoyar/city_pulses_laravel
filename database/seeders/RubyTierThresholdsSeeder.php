<?php

namespace Database\Seeders;

use App\Models\RubyTierThreshold;
use Illuminate\Database\Seeder;

class RubyTierThresholdsSeeder extends Seeder
{
    public function run(): void
    {
        $tiers = [
            ['tier_name' => 'silver', 'threshold_points' => 5000],
            ['tier_name' => 'gold', 'threshold_points' => 10000],
            ['tier_name' => 'diamond', 'threshold_points' => 15000],
            ['tier_name' => 'red', 'threshold_points' => 20000],
        ];

        foreach ($tiers as $tier) {
            RubyTierThreshold::updateOrCreate(
                ['tier_name' => $tier['tier_name']],
                ['threshold_points' => $tier['threshold_points']]
            );
        }
    }
}
