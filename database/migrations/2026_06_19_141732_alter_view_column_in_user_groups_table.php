<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_groups', function (Blueprint $table) {
            // Alter the column to be not nullable and default to true
            $table->boolean('view')->default(true)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('user_groups', function (Blueprint $table) {
            // Revert back to nullable and default false
            $table->boolean('view')->default(false)->nullable()->change();
        });
    }
};
