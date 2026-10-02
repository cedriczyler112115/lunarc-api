<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CarRentalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = \App\Models\User::where('email', 'admin@example.com')->first() ?? \App\Models\User::first();

        $sedanType = \App\Models\VehicleType::where('name', 'Sedan')->first();
        $suvType = \App\Models\VehicleType::where('name', 'SUV')->first();
        $pickupType = \App\Models\VehicleType::where('name', 'Pickup')->first();
        $mpvType = \App\Models\VehicleType::where('name', 'MPV')->first();

        $v1 = \App\Models\Vehicle::create([
            'user_id' => $admin?->id,
            'vehicle_type_id' => $sedanType?->id,
            'name' => 'Toyota Vios 1.5 G',
            'make' => 'Toyota',
            'model' => 'Vios',
            'year' => 2023,
            'license_plate' => 'KAA 8821',
            'color' => 'Silver Metallic',
            'transmission' => 'Automatic',
            'fuel_type' => 'Gasoline',
            'seats' => 5,
            'daily_rate' => 1800.00,
            'status' => 'available',
            'description' => 'Economical & smooth subcompact sedan perfect for city driving and long highway trips in Agusan del Norte.',
        ]);

        $v2 = \App\Models\Vehicle::create([
            'user_id' => $admin?->id,
            'vehicle_type_id' => $suvType?->id,
            'name' => 'Mitsubishi Montero Sport GT',
            'make' => 'Mitsubishi',
            'model' => 'Montero Sport',
            'year' => 2024,
            'license_plate' => 'LNR 4090',
            'color' => 'Pearl White',
            'transmission' => 'Automatic',
            'fuel_type' => 'Diesel',
            'seats' => 7,
            'daily_rate' => 3500.00,
            'status' => 'available',
            'description' => 'Premium 7-seater SUV with high ground clearance, leather interiors, and advanced safety features for family tours.',
        ]);

        $v3 = \App\Models\Vehicle::create([
            'user_id' => $admin?->id,
            'vehicle_type_id' => $pickupType?->id,
            'name' => 'Nissan Navara VL 4x4',
            'make' => 'Nissan',
            'model' => 'Navara',
            'year' => 2023,
            'license_plate' => 'NVR 7712',
            'color' => 'Stealth Grey',
            'transmission' => 'Automatic',
            'fuel_type' => 'Diesel',
            'seats' => 5,
            'daily_rate' => 3200.00,
            'status' => 'available',
            'description' => 'Heavy-duty 4x4 pickup with multi-link suspension, payload capacity, and around-view monitor for rugged terrain.',
        ]);

        $v4 = \App\Models\Vehicle::create([
            'user_id' => $admin?->id,
            'vehicle_type_id' => $sedanType?->id,
            'name' => 'Honda City RS',
            'make' => 'Honda',
            'model' => 'City',
            'year' => 2023,
            'license_plate' => 'HND 5543',
            'color' => 'Ignite Red',
            'transmission' => 'Automatic',
            'fuel_type' => 'Gasoline',
            'seats' => 5,
            'daily_rate' => 2000.00,
            'status' => 'available',
            'description' => 'Sporty sedan with RS styling, paddle shifters, and ultra-responsive handling.',
        ]);

        $v5 = \App\Models\Vehicle::create([
            'user_id' => $admin?->id,
            'vehicle_type_id' => $mpvType?->id,
            'name' => 'Suzuki Ertiga Hybrid',
            'make' => 'Suzuki',
            'model' => 'Ertiga',
            'year' => 2024,
            'license_plate' => 'SZK 1192',
            'color' => 'Cool Black',
            'transmission' => 'Automatic',
            'fuel_type' => 'Hybrid',
            'seats' => 7,
            'daily_rate' => 2200.00,
            'status' => 'available',
            'description' => 'Smart hybrid MPV with impressive fuel efficiency and flexible 7-passenger seating layout.',
        ]);

        // Retrieve destinations for seeded bookings
        $destButuan = \App\Models\Destination::where('city', 'Butuan City')->first();
        $destSiargao = \App\Models\Destination::where('city', 'General Luna (Siargao)')->first();
        $destAgusanSur = \App\Models\Destination::where('city', 'San Francisco')->first();

        // Create Seeded Bookings for October 2026
        \App\Models\Booking::create([
            'booking_code' => 'LNR-20261002-881A',
            'vehicle_id' => $v1->id,
            'destination_id' => $destButuan?->id,
            'destination' => $destButuan ? "{$destButuan->region} — {$destButuan->province} — {$destButuan->city}" : 'Region XIII (Caraga) — Agusan del Norte — Butuan City',
            'destination_rate' => $destButuan?->destination_rate ?? 0.00,
            'customer_name' => 'Juan Dela Cruz',
            'customer_email' => 'juan.delacruz@example.com',
            'customer_phone' => '+63 917 123 4567',
            'start_date' => '2026-10-05',
            'end_date' => '2026-10-08',
            'total_days' => 3,
            'daily_rate' => 1800.00,
            'total_price' => (3 * 1800.00) + ($destButuan?->destination_rate ?? 0.00),
            'status' => 'confirmed',
            'notes' => 'Airport pickup at Bancasi Airport (BXU) at 9:00 AM.',
        ]);

        \App\Models\Booking::create([
            'booking_code' => 'LNR-20261002-99B2',
            'vehicle_id' => $v2->id,
            'destination_id' => $destSiargao?->id,
            'destination' => $destSiargao ? "{$destSiargao->region} — {$destSiargao->province} — {$destSiargao->city}" : 'Region XIII (Caraga) — Surigao del Norte — General Luna (Siargao)',
            'destination_rate' => $destSiargao?->destination_rate ?? 4500.00,
            'customer_name' => 'Maria Santos',
            'customer_email' => 'maria.santos@example.com',
            'customer_phone' => '+63 928 987 6543',
            'start_date' => '2026-10-10',
            'end_date' => '2026-10-15',
            'total_days' => 5,
            'daily_rate' => 3500.00,
            'total_price' => (5 * 3500.00) + ($destSiargao?->destination_rate ?? 4500.00),
            'status' => 'confirmed',
            'notes' => 'Family vacation trip to Siargao / Surigao route.',
        ]);

        \App\Models\Booking::create([
            'booking_code' => 'LNR-20261002-77C3',
            'vehicle_id' => $v3->id,
            'destination_id' => $destAgusanSur?->id,
            'destination' => $destAgusanSur ? "{$destAgusanSur->region} — {$destAgusanSur->province} — {$destAgusanSur->city}" : 'Region XIII (Caraga) — Agusan del Sur — San Francisco',
            'destination_rate' => $destAgusanSur?->destination_rate ?? 1500.00,
            'customer_name' => 'Engr. Roberto Gomez',
            'customer_email' => 'roberto.gomez@example.com',
            'customer_phone' => '+63 919 444 5566',
            'start_date' => '2026-10-12',
            'end_date' => '2026-10-14',
            'total_days' => 2,
            'daily_rate' => 3200.00,
            'total_price' => (2 * 3200.00) + ($destAgusanSur?->destination_rate ?? 1500.00),
            'status' => 'confirmed',
            'notes' => 'Site inspection project in Agusan del Sur.',
        ]);
    }

}
