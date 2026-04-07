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
          Schema::create('turistic_places', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slogan');
            $table->string('cover');
            $table->longText('description');
            $table->longText('localization');
            $table->decimal('lat', 10, 8); 
            $table->decimal('lng', 11, 8); 
            $table->longText('Weather');
            $table->string('Weather_img');
            $table->longText('features');
            $table->string('features_img');
            $table->longText('flora');
            $table->string('flora_img');
            $table->longText('estructure');
            $table->string('estructure_img');
            $table->longText('tips');
            $table->boolean('terminos')->default(false);
            $table->boolean('politicas')->default(false);
            

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

         

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
