<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('turistic_places', 'opening_status')) {
            Schema::table('turistic_places', function (Blueprint $table) {
                $table->boolean('opening_status')->default(true)->after('open_days');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('turistic_places', 'opening_status')) {
            Schema::table('turistic_places', function (Blueprint $table) {
                $table->dropColumn('opening_status');
            });
        }
    }
};
