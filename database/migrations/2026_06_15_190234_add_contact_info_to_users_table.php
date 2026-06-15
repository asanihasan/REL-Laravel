<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Adds the nullable email and telegram_id columns
            $table->string('email')->nullable()->after('username');
            $table->string('telegram_id')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drops the columns if you ever roll back the migration
            $table->dropColumn(['email', 'telegram_id']);
        });
    }
};