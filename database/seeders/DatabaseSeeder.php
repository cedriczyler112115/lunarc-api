<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed default Admin user
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'is_approved' => true,
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Seed default Test user
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'is_approved' => true,
                'is_admin' => false,
                'email_verified_at' => now(),
            ]
        );

        // Seed a sample pending user awaiting approval
        User::updateOrCreate(
            ['email' => 'juan.applicant@example.com'],
            [
                'name' => 'Juan Applicant',
                'password' => Hash::make('password'),
                'is_approved' => false,
                'is_admin' => false,
            ]
        );

        $this->call([
            VehicleTypeSeeder::class,
            DestinationSeeder::class,
            CarRentalSeeder::class,
        ]);
    }
}
