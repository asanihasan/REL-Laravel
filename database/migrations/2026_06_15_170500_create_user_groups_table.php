<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_groups', function (Blueprint $table) {
            $table->id(); 
            $table->string('name');
            
            $table->boolean('control')->default(false);
            $table->boolean('engine')->default(false);
            $table->boolean('pump')->default(false);
            $table->boolean('historical')->default(false);
            $table->boolean('data_manager')->default(false);
            $table->boolean('user_management')->default(false);
            
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_groups');
    }
};