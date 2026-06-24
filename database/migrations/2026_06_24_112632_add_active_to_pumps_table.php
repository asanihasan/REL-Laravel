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
        Schema::table('pumps', function (Blueprint $table) {
            $table->boolean('active')->default(true)->after('id'); 
            $table->integer('fault_code')->nullable()->after('active'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pumps', function (Blueprint $table) {
            // Drop both columns at the same time if we rollback
            $table->dropColumn(['active', 'fault_code']);
        });
    }
};