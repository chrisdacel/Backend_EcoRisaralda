<?php

namespace Database\Seeders;

use App\Models\LabelPlace;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlaceLabel extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $label_place= [

  

    // 1. Parque Regional Natural Ucumarí
    ['place_id' => 1, 'label_id' => 1],
    ['place_id' => 1, 'label_id' => 2],
    ['place_id' => 1, 'label_id' => 5],
    ['place_id' => 1, 'label_id' => 6],
    ['place_id' => 1, 'label_id' => 8],

    // 2. Termales de Santa Rosa
    ['place_id' => 2, 'label_id' => 8],

    // 3. Santuario Otún Quimbaya
    ['place_id' => 3, 'label_id' => 1],
    ['place_id' => 3, 'label_id' => 2],
    ['place_id' => 3, 'label_id' => 5],
    ['place_id' => 3, 'label_id' => 6],
    ['place_id' => 3, 'label_id' => 8],

    // 4. Laguna del Otún
    ['place_id' => 4, 'label_id' => 1],
    ['place_id' => 4, 'label_id' => 6],

    // 5. Reserva Bosque de Yotoco
    ['place_id' => 5, 'label_id' => 1],
    ['place_id' => 5, 'label_id' => 5],
    ['place_id' => 5, 'label_id' => 6],
    ['place_id' => 5, 'label_id' => 8],

    // 6. Mistrató
    ['place_id' => 6, 'label_id' => 1],
    ['place_id' => 6, 'label_id' => 3],
    ['place_id' => 6, 'label_id' => 8],

    // 7. Chorros de Don Lolo
    ['place_id' => 7, 'label_id' => 1],
    ['place_id' => 7, 'label_id' => 8],

    // 8. Parque Consotá
    ['place_id' => 8, 'label_id' => 1],
    ['place_id' => 8, 'label_id' => 8],

    // 9. Jardín Botánico UTP
    ['place_id' => 9, 'label_id' => 1],
    ['place_id' => 9, 'label_id' => 5],
    ['place_id' => 9, 'label_id' => 8],

    // 10. Parque Nacional Natural Tatamá
    ['place_id' => 10, 'label_id' => 1],
    ['place_id' => 10, 'label_id' => 2],
    ['place_id' => 10, 'label_id' => 5],
    ['place_id' => 10, 'label_id' => 6],

    // 11. Pueblo Rico
    ['place_id' => 11, 'label_id' => 1],
    ['place_id' => 11, 'label_id' => 2],
    ['place_id' => 11, 'label_id' => 6],

    // 12. Parque Nacional Natural Los Nevados
    ['place_id' => 12, 'label_id' => 1],
    ['place_id' => 12, 'label_id' => 6],

    // 13. Ecohotel Los Lagos
    ['place_id' => 13, 'label_id' => 1],
    ['place_id' => 13, 'label_id' => 2],
    ['place_id' => 13, 'label_id' => 8],

    // 14. Reserva Barbas Bremen
    ['place_id' => 14, 'label_id' => 1],
    ['place_id' => 14, 'label_id' => 2],
    ['place_id' => 14, 'label_id' => 6],

    // 15. Los Guayacanes Restaurante
    // (sin etiquetas asignadas)

    // 16. Gran Reserva Wabanta
    ['place_id' => 16, 'label_id' => 1],
    ['place_id' => 16, 'label_id' => 2],
    ['place_id' => 16, 'label_id' => 6],
    ['place_id' => 16, 'label_id' => 8],
];


         foreach ($label_place as $item) {
    LabelPlace::create([
        'place_id' => $item['place_id'],
        'label_id' => $item['label_id'],
    ]);
}

    }
}
