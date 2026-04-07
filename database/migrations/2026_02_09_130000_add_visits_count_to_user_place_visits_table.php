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
        Schema::table('user_place_visits', function (Blueprint $table) {
            $table->unsignedInteger('visits_count')->default(1)->after('visited_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_place_visits', function (Blueprint $table) {
            $table->dropColumn('visits_count');
        });
    }
};
