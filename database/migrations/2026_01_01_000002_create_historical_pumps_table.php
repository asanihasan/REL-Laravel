<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('historical_pumps', function (Blueprint $table) {
            $table->id();
            // Foreign key to pumps table
            $table->string('pump_id', 32);
            $table->foreign('pump_id')->references('id')->on('pumps')->onDelete('cascade');
            
            $table->timestamp('ts')->useCurrent(); // Snapshot timestamp

            // Replicate columns from pumps table to store history
            // JSONB Columns
            $table->jsonb('pressure_or_flow')->nullable();
            $table->jsonb('auto_manual_status')->nullable();
            $table->jsonb('digital_inputs')->nullable();
            $table->boolean('coolant_level_probe')->default(false);

            // Telemetry Fields
            $table->float('percent_load')->default(0);
            $table->integer('rpm')->default(0);
            $table->float('engine_hours')->default(0);
            $table->float('fuel_rate')->default(0);
            $table->float('fuel_level')->default(0);
            $table->float('coolant_level')->default(0);
            $table->float('coolant_temp')->default(0);
            $table->float('oil_temp')->default(0);
            $table->float('intake_temp')->default(0);
            $table->float('pump_temp')->default(0);
            $table->float('gearbox_temp')->default(0);
            $table->float('engine_temp_mech')->default(0);
            $table->float('oil_pressure')->default(0);
            $table->float('boost_pressure')->default(0);
            $table->float('suction_pressure')->default(0);
            $table->float('pump_press2')->default(0);
            $table->float('engine_pressure_mech')->default(0);
            $table->float('electrical_potential')->default(0);
            $table->float('battery_potential')->default(0);
            $table->float('battery_volts_mech')->default(0);
            $table->float('machine_hours')->default(0);
            $table->float('aux_hours')->default(0);
            $table->float('dam_level')->default(0);
            $table->integer('engine_speed_mech')->default(0);
            $table->float('engine_hours_mech')->default(0);
            $table->integer('config_number')->default(0);
            $table->integer('asset_tag')->default(0);
            $table->integer('heartbeat')->default(0);
            $table->integer('firmware_version')->default(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('historical_pumps');
    }
};