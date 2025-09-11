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
        'approved_by',
        'total_price',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Add this relationship
    public function bookingGuestDetails(): HasMany
    {
        return $this->hasMany(BookingGuestDetails::class, 'day_tour_log_details_id');
    }

}
