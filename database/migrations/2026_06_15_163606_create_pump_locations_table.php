<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pump_locations', function (Blueprint $table) {
            // Auto-incrementing primary key (replaces the string ID)
            $table->id();
            
            // Foreign key linking to the 'pumps' table
            // cascadeOnDelete() ensures that if a pump is deleted, its location data is also cleaned up
            $table->foreignId('pump_id')->constrained('pumps')->cascadeOnDelete();
            
            // Decimal precision for map coordinates
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            
            // Auto-updates to the current time whenever the row is created or modified
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pump_locations');
    }
};