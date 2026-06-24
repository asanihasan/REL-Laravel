<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricalAlert extends Model
{
    // Point explicitly to the table
    protected $table = 'historical_alerts';

    // Disable default created_at / updated_at since you use a custom 'ts' column
    public $timestamps = false;

    protected $guarded = [];

    // Define relationship back to the Pump
    public function pump()
    {
        return $this->belongsTo(Pump::class, 'pump_id', 'id');
    }
}