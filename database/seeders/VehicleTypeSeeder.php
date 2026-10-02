<?php

namespace Database\Seeders;

use App\Models\VehicleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VehicleTypeSeeder extends Seeder
{
    /**
     * Seed default dynamic vehicle types.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Sedan', 'description' => 'Subcompact & Compact 4-door passenger sedans.'],
            ['name' => 'SUV', 'description' => 'Sport Utility Vehicles (Mid-size, Full-size 7-seaters).'],
            ['name' => 'MPV', 'description' => 'Multi-Purpose Vehicles & Family Compact People Movers.'],
            ['name' => 'Van', 'description' => 'Passenger Van (Commuter / HiAce / Urvan 10-15 seaters).'],
            ['name' => 'Pickup', 'description' => '4x2 & 4x4 Heavy-Duty Utility Pickups.'],
        ];

        foreach ($types as $t) {
            VehicleType::updateOrCreate(
                ['name' => $t['name']],
                [
                    'slug' => Str::slug($t['name']),
                    'description' => $t['description'],
                ]
            );
        }
    }
}
