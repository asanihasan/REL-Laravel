<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pump_locations', function (Blueprint $table) {
            // Adds the unique constraint
            $table->unique('pump_id');
        });
    }

    public function down(): void
    {
        Schema::table('pump_locations', function (Blueprint $table) {
            // Drops the unique constraint if you rollback
            $table->dropUnique(['pump_id']);
        });
    }
};