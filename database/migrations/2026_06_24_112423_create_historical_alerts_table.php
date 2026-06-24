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
        Schema::create('historical_alerts', function (Blueprint $table) {
            $table->id();
            
            // Sets the default value to the database's current timestamp (NOW())
            $table->timestamp('ts')->useCurrent(); 
            
            // Foreign key linking to your existing 'pumps' table
            $table->foreignId('pump_id')->constrained('pumps')->onDelete('cascade'); 
            
            $table->string('alert_type');
            $table->string('description', 256);
            
            // Defaulting to false is a good practice for booleans unless specified otherwise
            $table->boolean('email')->default(false); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historical_alerts');
    }
};