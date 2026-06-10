<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('flood_events', function (Blueprint $table) {
            $table->id();
            $table->string('location', 100)->nullable();
            $table->string('water_level', 20);
            $table->boolean('alert_sent')->default(false);
            
            // Client's DB only has created_at (timestamp), no updated_at
            $table->timestamp('timestamp')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flood_events');
    }
};