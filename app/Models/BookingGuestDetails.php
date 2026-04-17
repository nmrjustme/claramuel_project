<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingGuestDetails extends Model
{
    protected $table = 'booking_guest_details';
    
    protected $fillable = [
        'guest_type_id',
        'facility_booking_log_id',
        'day_tour_log_details_id', // Add this
        'facility_id',
        'quantity',
        'facility_quantity',
    ];
    
    public function guestType(): BelongsTo
    {
        return $this->belongsTo(GuestType::class, 'guest_type_id');
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'facility_id');
    }

     // Add this relationship
    public function dayTourLog(): BelongsTo
    {
        return $this->belongsTo(DayTourLogDetails::class, 'day_tour_log_details_id');
    }
    
}

