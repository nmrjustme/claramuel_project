<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @property-read \Illuminate\Support\Carbon $date_from
 * @property-read \Illuminate\Support\Carbon $date_to
 * @property-read \Illuminate\Support\Carbon $expires_at
 */

class RoomHold extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'date_from',
        'date_to',
        'session_id',
        'expires_at',
        'status'
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
        'expires_at' => 'datetime'
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'pending')
                     ->where('expires_at', '>', now());
    }

    public function isExpired()
    {
        return $this->expires_at->isPast() || $this->status === 'expired';
    }
}