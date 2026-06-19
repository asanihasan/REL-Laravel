<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    use HasFactory;

    // The fields that can be mass-assigned when creating or updating a group
    protected $fillable = [
        'name',
        'view',
        'control',
        'historical',
        'data_manager',
    ];

    // Automatically cast these columns to strict booleans so they work perfectly with your checkboxes
    protected $casts = [
        'view' => 'boolean',
        'control' => 'boolean',
        'historical' => 'boolean',
        'data_manager' => 'boolean',
    ];

    // Relationship: A User Group can have multiple Users assigned to it
    public function users()
    {
        return $this->hasMany(User::class, 'user_group_id');
    }
}
