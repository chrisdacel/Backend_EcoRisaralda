<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('turistic_places', function (Blueprint $table) {
            $table->string('approval_status', 20)->default('pending')->index();
        });

        Schema::table('place_events', function (Blueprint $table) {
            $table->string('approval_status', 20)->default('pending')->index();
        });

        DB::table('turistic_places')->update(['approval_status' => 'approved']);
        DB::table('place_events')->update(['approval_status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('turistic_places', function (Blueprint $table) {
            $table->dropColumn('approval_status');
        });

        Schema::table('place_events', function (Blueprint $table) {
            $table->dropColumn('approval_status');
        });
    }
};
