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
        $this->call(CountrySeeder::class);
        // User::factory(10)->create();

        // Crear o actualizar usuarios por email
        $users = [
            [
                'name' => 'Test User',
                'last_name'=>'apellido',
                'country_id'=>'1',
                'date_of_birth'=>'2025-10-02',
                'email_verified_at'=>now(),
                'password' => Hash::make('password123'),
                'role'=>'operator',
                'email' => 'test@example.com',
            ],
            [
                'name' => 'Test User2',
                'last_name'=>'apellido2',
                'country_id'=>'1',
                'date_of_birth'=>'2025-10-02',
                'email_verified_at'=>now(),
                'password' => Hash::make('password123'),
                'role'=>'user',
                'email' => 'test2@example.com',
            ],
            [
                'name' => 'Test User3',
                'last_name'=>'apellido3',
                'country_id'=>'1',
                'date_of_birth'=>'2025-10-02',
                'email_verified_at'=>now(),
                'password' => Hash::make('password123'),
                'role'=>'user',
                'email' => 'test3@example.com',
            ],
            [
                'name' => 'Test User4',
                'last_name'=>'apellido4',
                'country_id'=>'1',
                'date_of_birth'=>'2025-10-02',
                'email_verified_at'=>now(),
                'password' => Hash::make('password123'),
                'role'=>'user',
                'email' => 'test4@example.com',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        // Crear admin explícitamente
        $this->call(AdminSeeder::class);
        // Ejecutar los seeders de preferencias
        $this->call(PreferenceSeeder::class);
        // Ejecutar los seeders de lugares turísticos
        $this->call(TurusticPlaceSeeder::class);
        // Ejecutar los seeders de etiquetas de lugares
        $this->call(PlaceLabel::class);
    }
}
