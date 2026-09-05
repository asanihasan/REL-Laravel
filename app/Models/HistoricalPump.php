<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricalPump extends Model
{
    protected $table = 'historical_pumps';

    public $timestamps = false;

    protected $fillable = [
        'pump_id',
        'ts',
        'pressure_or_flow',
        'auto_manual_status',
        'digital_inputs',
        'coolant_level_probe',
        'percent_load',
        'rpm',
        'engine_hours',
        'fuel_rate',
        'fuel_level',
        'coolant_level',
        'coolant_temp',
        'oil_temp',
        'intake_temp',
        'pump_temp',
        'gearbox_temp',
        'engine_temp_mech',
        'oil_pressure',
        'boost_pressure',
        'suction_pressure',
        'pump_press2',
        'engine_pressure_mech',
        'electrical_potential',
        'battery_potential',
        'battery_volts_mech',
        'machine_hours',
        'aux_hours',
        'dam_level',
        'engine_speed_mech',
        'engine_hours_mech',
        'config_number',
        'asset_tag',
        'heartbeat',
        'firmware_version',
        'latitude',
        'longitude',
        'location',
        'fault_code',
    ];

    protected $casts = [
        'ts'                  => 'datetime',
        'pressure_or_flow'    => 'array', 
        'auto_manual_status'  => 'array', 
        'digital_inputs'      => 'array', 
        'coolant_level_probe' => 'boolean',
        'percent_load'        => 'float',
        'engine_hours'        => 'float',
        'fuel_rate'           => 'float',
        'fuel_level'          => 'float',
        'coolant_level'       => 'float',
        'coolant_temp'        => 'float',
        'oil_temp'            => 'float',
        'intake_temp'         => 'float',
        'pump_temp'           => 'float',
        'gearbox_temp'        => 'float',
        'engine_temp_mech'    => 'float',
        'oil_pressure'        => 'float',
        'boost_pressure'      => 'float',
        'suction_pressure'    => 'float',
        'pump_press2'         => 'float',
        'engine_pressure_mech'=> 'float',
        'electrical_potential'=> 'float',
        'battery_potential'   => 'float',
        'battery_volts_mech'  => 'float',
        'machine_hours'       => 'float',
        'aux_hours'           => 'float',
        'dam_level'           => 'float',
        'engine_hours_mech'   => 'float',
        'latitude'            => 'float',
        'longitude'           => 'float',
        'rpm'                 => 'integer',
        'engine_speed_mech'   => 'integer',
        'config_number'       => 'integer',
        'asset_tag'           => 'integer',
        'heartbeat'           => 'integer',
        'firmware_version'    => 'integer',
        'fault_code'          => 'integer',
    ];

    // Append the custom attribute to the model's array and JSON forms
    protected $appends = ['fault_status'];

    // Define the accessor for 'fault_status'
    public function getFaultStatusAttribute()
    {
        $statuses = [
            0  => 'Normal Operation',
            1  => 'Low Oil Pressure',
            2  => 'High Engine Temp.',
            3  => 'Auxiliary 3',
            4  => 'Loss of Flow Sw.',
            5  => 'Alt Failure',
            6  => 'Coolant Level Low',
            7  => 'Overspeed',
            8  => 'Underspeed',
            9  => 'Bad or NO RPM',
            10 => 'Failed Crank Attempts',
            11 => 'Aux. Input 1',
            12 => 'Aux. Input 2',
            13 => 'Aux. Input 3',
            14 => 'Low Fuel Level',
            15 => 'Low Pump Press #2',
            16 => 'Max Pump Press #2',
            17 => 'Low Pump Pressure',
            18 => 'Max Pump Pressure',
            19 => 'CAN BUS Failure',
            20 => 'Pump Temperature',
            21 => 'Internal Protection',
            22 => 'Suction Pressure',
            23 => 'Check ECU Codes',
            24 => 'Timer Complete',
            25 => 'Normal Shutdown 25',
            26 => 'Dam Level Sensor Error',
            29 => 'Low Flow',
            30 => 'High Flow',
            31 => 'Stagnant Timer',
            32 => 'Normal Shutdown 32',
            33 => 'Pressure Stagnant',
            34 => 'Gear box Temp',
        ];

        return $statuses[$this->fault_code] ?? 'Unknown Fault';
    }
}