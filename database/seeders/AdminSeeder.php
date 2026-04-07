<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@ecorisaralda.com'],
            [
                'name' => 'Admin EcoRisaralda',
                'last_name' => 'Admin',
                'country_id' => 1,
                'date_of_birth' => '1990-01-01',
                'password' => Hash::make('ecorisaralda123'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
                'first_time_preferences' => true,
            ]
        );
    }
}
