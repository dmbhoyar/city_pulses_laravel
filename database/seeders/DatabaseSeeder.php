<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\City;
use App\Models\Market;
use App\Models\Shop;
use App\Models\Farming;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default superadmin from environment config
        $adminEmail = env('ADMIN_EMAIL', 'admin@citypulses.com');
        User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'mobile_number' => '9999999999',
                'role' => 'superadmin',
                'password' => env('ADMIN_PASSWORD', 'Admin@123'),
            ]
        );

        // Seed cities (used for city selector)
        $washim = City::firstOrCreate(
            ['name' => 'Washim'],
            [
                'latitude' => 20.1046,
                'longitude' => 77.1426,
                'agmarknet_district' => 'vashim',
                'agmarknet_state' => 'Maharashtra',
                'agmarknet_market' => 'Washim (Main)',
            ]
        );

        $mangrulpir = City::firstOrCreate(
            ['name' => 'Mangrulpir'],
            [
                'latitude' => 20.3167,
                'longitude' => 77.5047,
                'agmarknet_district' => 'vashim',
                'agmarknet_state' => 'Maharashtra',
                'agmarknet_market' => 'Mangrulpir',
            ]
        );

        $karanja = City::firstOrCreate(
            ['name' => 'Karanja'],
            [
                'latitude' => 20.4833,
                'longitude' => 77.4833,
                'agmarknet_district' => 'vashim',
                'agmarknet_state' => 'Maharashtra',
                'agmarknet_market' => 'Karanja (APMC)',
            ]
        );

        $amravati = City::firstOrCreate(
            ['name' => 'Amravati'],
            [
                'latitude' => 20.9374,
                'longitude' => 77.7796,
                'agmarknet_district' => 'Amarawati',
                'agmarknet_state' => 'Maharashtra',
                'agmarknet_market' => 'Amarawati',
            ]
        );

        $shelubajar = City::firstOrCreate(
            ['name' => 'Shelubajar'],
            [
                'latitude' => 20.2,
                'longitude' => 77.3,
                'agmarknet_district' => 'vashim',
                'agmarknet_state' => 'Maharashtra',
                'agmarknet_market' => 'Shelubajar',
            ]
        );

        $akola = City::firstOrCreate(
            ['name' => 'Akola'],
            [
                'latitude' => 20.7167,
                'longitude' => 77.0,
                'agmarknet_district' => 'Akola',
                'agmarknet_state' => 'Maharashtra',
                'agmarknet_market' => 'Akola',
            ]
        );

        // Avoid unused variable warnings in static analyzers
        unset($mangrulpir, $shelubajar, $akola);

        // Markets (attach to City records)
        Market::firstOrCreate(
            ['city_id' => $washim->id],
            [
                'city' => 'Washim (Main)',
                'district' => $washim->agmarknet_district,
                'latitude' => $washim->latitude,
                'longitude' => $washim->longitude,
                'rate' => 123.45,
            ]
        );

        Market::firstOrCreate(
            ['city_id' => $amravati->id],
            [
                'city' => 'Amravati',
                'district' => $amravati->agmarknet_district,
                'latitude' => $amravati->latitude,
                'longitude' => $amravati->longitude,
                'rate' => 130.10,
            ]
        );

        Market::firstOrCreate(
            ['city_id' => $karanja->id],
            [
                'city' => 'Karanja (APMC)',
                'district' => $karanja->agmarknet_district,
                'latitude' => $karanja->latitude,
                'longitude' => $karanja->longitude,
                'rate' => 110.75,
            ]
        );

        // Demo users for testing
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'mobile_number' => '9999999999',
                'password' => 'password123',
                'role' => 'superadmin',
            ]
        );

        $shopOwner = User::firstOrCreate(
            ['email' => 'shopowner@example.com'],
            [
                'first_name' => 'Shop',
                'last_name' => 'Owner',
                'mobile_number' => '8888888888',
                'password' => 'password123',
                'role' => 'shopowner',
            ]
        );

        // Demo shop for shopowner
        if ($shopOwner->shops()->count() === 0) {
            Shop::create([
                'name' => 'Demo Shop',
                'description' => 'Demo local shop',
                'phone' => '0000000000',
                'address' => 'Demo address',
                'user_id' => $shopOwner->id,
                'city_id' => $washim->id,
            ]);
        }

        // Demo farming notes
        Farming::firstOrCreate(
            ['title' => 'Soya bean harvest tips'],
            [
                'content' => 'Ensure timely sowing and use certified seeds. Monitor moisture.',
                'city_id' => $washim->id,
            ]
        );

        Farming::firstOrCreate(
            ['title' => 'Bee keeping basics'],
            [
                'content' => 'Bees increase pollination; keep boxes shaded and water available.',
            ]
        );

        $this->command->info('Core demo seed data ensured.');
    }
}
