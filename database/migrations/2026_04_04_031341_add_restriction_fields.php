<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('restricted_by_role', 20)->nullable()->after('is_restricted');
            $table->string('restriction_reason', 50)->nullable()->after('restricted_by_role');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['restricted_by_role', 'restriction_reason']);
        });
    }
};
