<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FloodEvent extends Model
{
    protected $fillable = [
        'location', // Swapped from barangay_id
        'warning_level',
        'alert_sent'
    ];

    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class);
    }
}