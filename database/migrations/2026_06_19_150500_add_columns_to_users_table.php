<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // First and Last Name
            $table->string('first_name')->after('username')->nullable();
            $table->string('last_name')->after('first_name')->nullable();

            // Telegram Integration
            $table->string('telegram_link_token')->nullable()->after('telegram_id');

            // Alert/Status boolean columns
            $table->boolean('engine_running')->default(false);
            $table->boolean('engine_stopped')->default(false);
            $table->boolean('high_rpm')->default(false);
            $table->boolean('low_rpm')->default(false);
            $table->boolean('low_fuel_level')->default(false);
            $table->boolean('location_change')->default(false);
            $table->boolean('modbus_comm_lost')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'telegram_id',
                'telegram_link_token',
                'engine_running',
                'engine_stopped',
                'high_rpm',
                'low_rpm',
                'low_fuel_level',
                'location_change',
                'modbus_comm_lost'
            ]);
        });
    }
};