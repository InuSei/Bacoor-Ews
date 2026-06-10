<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 100);
            $table->string('username', 50)->unique();
            $table->string('password');
            $table->string('phone_number', 15)->nullable();
            $table->string('email')->unique();
            $table->enum('role', ['admin', 'resident'])->default('resident');
            $table->string('barangay', 50)->nullable();
            
            // Client's DB only has created_at, no updated_at
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
    
};