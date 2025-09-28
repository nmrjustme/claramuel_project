<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DayTourLogDetails extends Model
{
    protected $table = 'day_tour_log_details';  
    
    protected $fillable = [
        'user_id',
        'date_tour',
        'status',
        'reservation_status',
        'approved_by',
        'total_price',
        'checked_in_at',
        'checked_out_at',
    ];

    // 👇 this is where casting goes
    protected $casts = [
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bookingGuestDetails(): HasMany
    {
        return $this->hasMany(BookingGuestDetails::class, 'day_tour_log_details_id');
    }

    public function isActiveNow(): bool
    {
        return $this->checked_in_at && !$this->checked_out_at;
    }
}
