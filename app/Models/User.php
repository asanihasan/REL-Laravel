<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'password',
        'user_group_id',
        'telegram_id',
        'telegram_link_token',
        'engine_running',
        'engine_stopped',
        'high_rpm',
        'low_rpm',
        'low_fuel_level',
        'location_change',
        'modbus_comm_lost',
    ];

    // Automatically cast the alert settings to strict booleans
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // (if you are using Laravel 10+, this might already be here)
        'engine_running' => 'boolean',
        'engine_stopped' => 'boolean',
        'high_rpm' => 'boolean',
        'low_rpm' => 'boolean',
        'low_fuel_level' => 'boolean',
        'location_change' => 'boolean',
        'modbus_comm_lost' => 'boolean',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    public function userGroup()
    {
        return $this->belongsTo(UserGroup::class, 'user_group_id');
    }

}
