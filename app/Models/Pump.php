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
    
    // Append custom computed attributes so they appear in JSON responses
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
        'modbus_status' => 'boolean',
        'last_update' => 'datetime',
        'updated_at' => 'datetime',
        'active' => 'boolean'
    ];

    // ==========================================
    // DATA RETRIEVAL VALIDATION (ACCESSORS)
    // ==========================================

    // 1. Array Validation
    public function getPressureOrFlowAttribute($value)
    {
        // Handle both raw JSON strings and pre-cast arrays to avoid conflicts with $casts
        $data = is_string($value) ? json_decode($value, true) : $value;

        if (is_array($data)) {
            if (isset($data['flow']) && $data['flow'] == -256) {
                $data['flow'] = 0;
            }
            if (isset($data['pressure']) && $data['pressure'] == -256) {
                $data['pressure'] = 0;
            }
        }

        return $data;
    }

    // 2. Temperature Validation
    public function getCoolantTempAttribute($value)
    {
        return $value == -40 ? 0 : $value;
    }

    public function getOilTempAttribute($value)
    {
        return $value == -273 ? 0 : $value;
    }

    public function getIntakeTempAttribute($value)
    {
        return $value == -40 ? 0 : $value;
    }

    public function getPumpTempAttribute($value)
    {
        return $value == -40 ? 0 : $value;
    }

    public function getGearboxTempAttribute($value)
    {
        return $value == -40 ? 0 : $value;
    }

    public function getEngineTempMechAttribute($value)
    {
        return $value == -40 ? 0 : $value;
    }

    // ==========================================
    // EXISTING COMPUTED ATTRIBUTES & SCOPES
    // ==========================================

    public function getStatusAttribute()
    {
        if ($this->modbus_status === false) {
            return 'offline';
        }

        if (!$this->last_update) return 'offline';
        return $this->last_update->diffInSeconds(now()) > 12 ? 'offline' : 'online';
    }

    public function getConnectionAttribute()
    {
        if (!$this->updated_at) return 'offline';
        return $this->updated_at->diffInSeconds(now()) > 12 ? 'offline' : 'online';
    }

    public function scopeWithLocation($query)
    {
        return $query->leftJoin('pump_locations', 'pumps.id', '=', 'pump_locations.pump_id')
                     ->addSelect('pumps.*', 'pump_locations.latitude', 'pump_locations.longitude');
    }
}
