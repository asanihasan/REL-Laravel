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
            
            $table->timestamp('ts')->useCurrent(); 
            
            // 1. Create the column as a string to match your pumps table
            $table->string('pump_id'); 
            
            // 2. Explicitly define the foreign key relationship
            $table->foreign('pump_id')->references('id')->on('pumps')->onDelete('cascade');
            
            $table->string('alert_type');
            $table->string('description', 256);
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