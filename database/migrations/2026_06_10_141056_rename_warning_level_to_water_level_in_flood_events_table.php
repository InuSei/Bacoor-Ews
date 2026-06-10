<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Renames production column securely without data drop.
     */
    public function up(): void
    {
        if (Schema::hasColumn('flood_events', 'warning_level')) {
            Schema::table('flood_events', function (Blueprint $table) {
                $table->renameColumn('warning_level', 'water_level');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('flood_events', 'water_level')) {
            Schema::table('flood_events', function (Blueprint $table) {
                $table->renameColumn('water_level', 'warning_level');
            });
        }
    }
};