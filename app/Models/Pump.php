<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pump extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;
    
    // All fields that can be mass assigned
    protected $fillable = [
        'id', 'name', 'location', 'last_update',
        'pressure_or_flow', 'auto_manual_status', 'digital_inputs',
        'percent_load', 'rpm', 'engine_hours', 'fuel_rate', 'fuel_level',
        'coolant_level', 'coolant_level_probe', 'coolant_temp', 'oil_temp',
        'oil_pressure', 'boost_pressure', 'intake_temp', 'pump_temp',
        'gearbox_temp', 'suction_pressure', 'pump_press2', 'electrical_potential',
        'battery_potential', 'machine_hours', 'aux_hours', 'dam_level',
        'engine_pressure_mech', 'engine_temp_mech', 'battery_volts_mech',
        'engine_speed_mech', 'engine_hours_mech', 'config_number',
        'asset_tag', 'heartbeat', 'firmware_version', 'modbus_status',
        'serial_number'
    ];

    // Automatically convert JSON columns to PHP arrays
    protected $casts = [
        'pressure_or_flow' => 'array',
        'auto_manual_status' => 'array',
        'digital_inputs' => 'array',
        'coolant_level_probe' => 'boolean',
        'last_update' => 'datetime',
    ];

    public function getStatusAttribute()
    {
        if (!$this->last_update) return 'offline';
        return $this->last_update->diffInSeconds(now()) > 10 ? 'offline' : 'online';
    }
}
