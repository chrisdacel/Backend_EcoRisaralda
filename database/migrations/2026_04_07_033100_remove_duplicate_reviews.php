<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Eliminar duplicados: dejar solo la reseña más reciente por (user_id, place_id)
        DB::statement('
            DELETE r1 FROM reviews r1
            INNER JOIN reviews r2
            WHERE
                r1.id < r2.id
                AND r1.user_id = r2.user_id
                AND r1.place_id = r2.place_id
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No se puede revertir la eliminación de duplicados
    }
};
