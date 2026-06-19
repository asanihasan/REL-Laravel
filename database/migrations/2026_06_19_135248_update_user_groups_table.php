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
        Schema::table('user_groups', function (Blueprint $table) {
            // 1. Drop the old columns
            $table->dropColumn(['engine', 'pump']);
            
            // 2. Add the new boolean column
            // Adding a default value prevents database crashes if you already have existing rows
            $table->boolean('view')->default(false); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_groups', function (Blueprint $table) {
            // 1. Remove the view column if we roll back
            $table->dropColumn('view');
            
            // 2. Re-add the old columns 
            // (Assuming they were booleans previously based on standard permission setups. 
            // If they were strings, change boolean() to string() here)
            $table->boolean('engine')->nullable();
            $table->boolean('pump')->nullable();
        });
    }
};
