<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    use HasFactory;

    // Tell Laravel to stop trying to update 'created_at' and 'updated_at'
    public $timestamps = false;

    protected $fillable = [
        'name',
        'view',
        'control',
        'historical',
        'data_manager',
        'administrator', // <-- Added here
    ];

    protected $casts = [
        'view' => 'boolean',
        'control' => 'boolean',
        'historical' => 'boolean',
        'data_manager' => 'boolean',
        'administrator' => 'boolean', // <-- Added here
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'user_group_id');
    }
}
