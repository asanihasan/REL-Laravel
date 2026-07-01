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
    
    // Append custom computed attributes so they appear in JSON responses (like your map API)
    protected $appends = ['status', 'connection'];
    
    protected $fillable = [
        'id', 'name', 'location', 'last_update', 'updated_at',
        'pressure_or_flow', 'auto_manual_status', 'digital_inputs',
        'percent_load', 'rpm', 'engine_hours', 'fuel_rate', 'fuel_level',
        'coolant_level', 'coolant_level_probe', 'coolant_temp', 'oil_temp',
        'oil_pressure', 'boost_pressure', 'intake_temp', 'pump_temp',
        'gearbox_temp', 'suction_pressure', 'pump_press2', 'electrical_potential',
        'battery_potential', 'machine_hours', 'aux_hours', 'dam_level',
        'engine_pressure_mech', 'engine_temp_mech', 'battery_volts_mech',
        'engine_speed_mech', 'engine_hours_mech', 'config_number',
        'asset_tag', 'heartbeat', 'firmware_version', 'modbus_status',
        'serial_number', 'active'
    ];

    protected $casts = [
        'pressure_or_flow' => 'array',
        'auto_manual_status' => 'array',
        'digital_inputs' => 'array',
        'coolant_level_probe' => 'boolean',
        'modbus_status' => 'boolean', // Added so strict boolean checks work
        'last_update' => 'datetime',
        'updated_at' => 'datetime',
        'active' => 'boolean'
    ];

    // 1. New Status: Offline if Modbus is false, otherwise checks freshness
    public function getStatusAttribute()
    {
        // If Modbus is completely offline, the pump is offline immediately
        if ($this->modbus_status === false) {
            return 'offline';
        }

        // If Modbus is true, rely on the 10-second data freshness check
        if (!$this->last_update) return 'offline';
        return $this->last_update->diffInSeconds(now()) > 10 ? 'offline' : 'online';
    }

    // 2. New Connection: Only cares about data freshness (your old logic)
    public function getConnectionAttribute()
    {
        if (!$this->last_update) return 'offline';
        return $this->last_update->diffInSeconds(now()) > 10 ? 'offline' : 'online';
    }

    public function scopeWithLocation($query)
    {
        return $query->leftJoin('pump_locations', 'pumps.id', '=', 'pump_locations.pump_id')
                     ->addSelect('pumps.*', 'pump_locations.latitude', 'pump_locations.longitude');
    }
}
