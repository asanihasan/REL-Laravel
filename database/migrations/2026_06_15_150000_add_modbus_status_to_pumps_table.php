<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pumps', function (Blueprint $table) {
            $table->boolean('modbus_status')->default(false)->after('id'); 
            
            // Adds the 64-character string column. 
            // 'nullable()' is highly recommended so existing records in your database 
            // don't cause an error when this runs.
            $table->string('serial_number', 64)->nullable()->after('modbus_status');
        });
    }

    public function down(): void
    {
        Schema::table('pumps', function (Blueprint $table) {
            // Drop both columns by passing them in an array
            $table->dropColumn(['modbus_status', 'serial_number']);
        });
    }
};