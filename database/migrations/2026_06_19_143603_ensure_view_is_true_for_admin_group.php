<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Force the view permission to true for User Group 1
        DB::table('user_groups')
            ->where('id', 1)
            ->update(['view' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert it back to false if you roll back this specific migration
        DB::table('user_groups')
            ->where('id', 1)
            ->update(['view' => false]);
    }
};
