<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pump_locations', function (Blueprint $table) {
            // Standard auto-incrementing ID for the pump_locations table itself
            $table->id(); 
            
            // 1. Create the foreign key column as a string to perfectly match pumps.id
            $table->string('pump_id', 32); 
            
            // 2. Explicitly declare the foreign key relationship
            $table->foreign('pump_id')
                  ->references('id')
                  ->on('pumps')
                  ->cascadeOnDelete();
            
            // Coordinates
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            
            // Timestamps
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pump_locations');
    }
};