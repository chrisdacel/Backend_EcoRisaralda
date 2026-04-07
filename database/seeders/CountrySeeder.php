<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => 'Colombia'],
            ['name' => 'Alemania'],
            ['name' => 'Arabia Saudita'],
            ['name' => 'Argentina'],
            ['name' => 'Australia'],
            ['name' => 'Austria'],
            ['name' => 'Bélgica'],
            ['name' => 'Belice'],
            ['name' => 'Bangladesh'],
            ['name' => 'Brasil'],
            ['name' => 'Bolivia'],
            ['name' => 'Canadá'],
            ['name' => 'Chile'],
            ['name' => 'China'],
            ['name' => 'Corea del Sur'],
            ['name' => 'Costa Rica'],
            ['name' => 'Cuba'],
            ['name' => 'Dinamarca'],
            ['name' => 'Ecuador'],
            ['name' => 'Egipto'],
            ['name' => 'El Salvador'],
            ['name' => 'Emiratos Árabes Unidos'],
            ['name' => 'España'],
            ['name' => 'Estados Unidos'],
            ['name' => 'Filipinas'],
            ['name' => 'Fiji'],
            ['name' => 'Francia'],
            ['name' => 'Grecia'],
            ['name' => 'Guatemala'],
            ['name' => 'Haití'],
            ['name' => 'Honduras'],
            ['name' => 'Hong Kong'],
            ['name' => 'Irán'],
            ['name' => 'Irlanda'],
            ['name' => 'Israel'],
            ['name' => 'Italia'],
            ['name' => 'Jamaíca'],
            ['name' => 'Japón'],
            ['name' => 'Kenia'],
            ['name' => 'Malasia'],
            ['name' => 'Marruecos'],
            ['name' => 'México'],
            ['name' => 'Nicaragua'],
            ['name' => 'Noruega'],
            ['name' => 'Nueva Caledonia'],
            ['name' => 'Nueva Zelanda'],
            ['name' => 'Países Bajos'],
            ['name' => 'Pakistán'],
            ['name' => 'Panamá'],
            ['name' => 'Paraguay'],
            ['name' => 'Perú'],
            ['name' => 'Portugal'],
            ['name' => 'Puerto Rico'],
            ['name' => 'Reino Unido'],
            ['name' => 'República Dominicana'],
            ['name' => 'Rusia'],
            ['name' => 'Singapur'],
            ['name' => 'Sri Lanka'],
            ['name' => 'Sudáfrica'],
            ['name' => 'Suecia'],
            ['name' => 'Suiza'],
            ['name' => 'Tailandia'],
            ['name' => 'Taiwán'],
            ['name' => 'Tanzania'],
            ['name' => 'Turquía'],
            ['name' => 'Uruguay'],
            ['name' => 'Venezuela'],
            ['name' => 'Vietnam']
        ];

        foreach ($countries as $country) {
            country::firstOrCreate(
                ['name' => $country['name']]
            );
        }
    }
}
