<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL only: alter enum to add 'inactive'
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY status ENUM('pending','approved','rejected','active','inactive') DEFAULT 'active'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'inactive' from enum
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY status ENUM('pending','approved','rejected','active') DEFAULT 'active'");
        }
    }
};
