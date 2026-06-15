<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pump_locations', function (Blueprint $table) {
            // String column with a max length of 32 as the Primary Key
            $table->string('id', 32)->primary();
            
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