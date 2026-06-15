<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Creates the column, links it to the user_groups table, and sets it 
            // to null if the associated user group is ever deleted.
            $table->foreignId('user_group_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('user_groups')
                  ->nullOnDelete(); 
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // You must drop the foreign key constraint before dropping the column
            $table->dropForeign(['user_group_id']);
            $table->dropColumn('user_group_id');
        });
    }
};