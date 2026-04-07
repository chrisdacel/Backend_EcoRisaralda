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
        Schema::table('turistic_places', function (Blueprint $table) {
            $table->string('contact_info', 500)->nullable()->after('tips');
            $table->json('open_days')->nullable()->after('contact_info');
            $table->string('opening_status')->default('open')->after('open_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turistic_places', function (Blueprint $table) {
            $table->dropColumn(['contact_info', 'open_days', 'opening_status']);
        });
    }
};
