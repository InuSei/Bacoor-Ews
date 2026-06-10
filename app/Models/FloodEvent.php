<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FloodEvent extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'location', // Swapped from barangay_id
        'water_level',
        'alert_sent'
    ];

    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class);
    }
}