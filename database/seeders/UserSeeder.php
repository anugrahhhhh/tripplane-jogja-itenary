<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@jogjatrip.test'],
            [
                'name' => 'Admin JogjaTrip',
                'password' => 'password',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'traveler@jogjatrip.test'],
            [
                'name' => 'Traveler Demo',
                'password' => 'password',
                'role' => 'traveler',
                'email_verified_at' => now(),
            ]
        );
    }
}
