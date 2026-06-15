<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    use HasFactory;

    // Disables standard Laravel created_at and updated_at behavior
    public $timestamps = false;

    // Specifies which columns are allowed to be bulk-inserted
    protected $fillable = [
        'name',
        'control',
        'engine',
        'pump',
        'historical',
        'data_manager',
        'user_management',
    ];

    // Ensures Eloquent treats these fields as strict booleans (true/false)
    protected $casts = [
        'control' => 'boolean',
        'engine' => 'boolean',
        'pump' => 'boolean',
        'historical' => 'boolean',
        'data_manager' => 'boolean',
        'user_management' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}