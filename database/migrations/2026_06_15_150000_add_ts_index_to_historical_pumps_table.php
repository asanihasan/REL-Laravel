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
        Schema::table('historical_pumps', function (Blueprint $table) {
            // Adds a basic index to the 'ts' column to speed up queries
            $table->index('ts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historical_pumps', function (Blueprint $table) {
            // Drops the index if you need to rollback. 
            // Passing it as an array is the safest way to let Laravel figure out the index name.
            $table->dropIndex(['ts']); 
        });
    }
};