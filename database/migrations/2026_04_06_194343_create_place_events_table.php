<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('place_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_id')
                ->constrained('turistic_places')
                ->onDelete('cascade');
            $table->string('title');
            $table->string('description', 1000)->nullable();
            $table->dateTime('starts_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('place_events');
    }
};
