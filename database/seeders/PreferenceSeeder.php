<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\preference;

class PreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $preferences = [
            [
                'name' => 'Senderismo',
                'image' => 'hiking',
                'color' => 'FF6B6B',
            ],
            [
                'name' => 'Avistamiento de aves',
                'image' => 'birdwatching',
                'color' => 'FFA500',
            ],
            [
                'name' => 'Ciclismo de montaña',
                'image' => 'biking',
                'color' => '4ECDC4',
            ],
            [
                'name' => 'Escalada o rappel',
                'image' => 'climbing',
                'color' => 'FFD93D',
            ],
            [
                'name' => 'Fauna y voluntariado',
                'image' => 'wildlife',
                'color' => '6BCB77',
            ],
            [
                'name' => 'Reservas naturales',
                'image' => 'reserves',
                'color' => '8B6F47',
            ],
            [
                'name' => 'Kayak o canoa',
                'image' => 'kayaking',
                'color' => '4D96FF',
            ],
            [
                'name' => 'Baños de bosque',
                'image' => 'forest_bathing',
                'color' => '52B788',
            ],
        ];

        foreach($preferences as $preference){
            preference::firstOrCreate(
                ['name' => $preference['name']],
                [
                    'image' => $preference['image'],
                    'color' => $preference['color'],
                ]
            );
        }
    }
}
