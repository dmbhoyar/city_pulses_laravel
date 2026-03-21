<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminUserSeeder extends Seeder
{
    /**
     * Seed the dedicated super admin user.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'dhananjaymb.d05@gmail.com'],
            [
                'first_name' => 'dhananjay',
                'last_name' => 'bhoyar',
                'mobile_number' => '9022281139',
                'role' => 'superadmin',
                'password' => env('SUPERADMIN_SEED_PASSWORD', 'Admin@123'),
            ]
        );
    }
}
