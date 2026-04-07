<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('turistic_places', function (Blueprint $table) {
            $table->string('archived_reason', 50)->nullable()->index();
        });

        Schema::table('place_events', function (Blueprint $table) {
            $table->string('archived_reason', 50)->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('turistic_places', function (Blueprint $table) {
            $table->dropColumn('archived_reason');
        });

        Schema::table('place_events', function (Blueprint $table) {
            $table->dropColumn('archived_reason');
        });
    }
};
