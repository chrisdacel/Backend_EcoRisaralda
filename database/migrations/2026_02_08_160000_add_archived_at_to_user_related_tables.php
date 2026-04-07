<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->index();
        });

        Schema::table('turistic_places', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->index();
        });

        Schema::table('place_events', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->index();
        });

        Schema::table('favorite_places', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->index();
        });

        Schema::table('preference_user', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->index();
        });

        Schema::table('review_reactions', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->index();
        });

        Schema::table('user_place_visits', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('archived_at');
        });

        Schema::table('turistic_places', function (Blueprint $table) {
            $table->dropColumn('archived_at');
        });

        Schema::table('place_events', function (Blueprint $table) {
            $table->dropColumn('archived_at');
        });

        Schema::table('favorite_places', function (Blueprint $table) {
            $table->dropColumn('archived_at');
        });

        Schema::table('preference_user', function (Blueprint $table) {
            $table->dropColumn('archived_at');
        });

        Schema::table('review_reactions', function (Blueprint $table) {
            $table->dropColumn('archived_at');
        });

        Schema::table('user_place_visits', function (Blueprint $table) {
            $table->dropColumn('archived_at');
        });
    }
};
