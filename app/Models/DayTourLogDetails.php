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
        'manual_discount_amount',
        'manual_discount_type',
        'manual_discount_value',
        'manual_discount_reason',
    ];

    // 👇 this is where casting goes
    protected $casts = [
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'manual_discount_amount' => 'decimal:2',
        'manual_discount_value' => 'decimal:2',
        'total_price' => 'decimal:2',
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