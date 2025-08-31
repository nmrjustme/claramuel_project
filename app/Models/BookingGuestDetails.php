<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingGuestDetails extends Model
{
    protected $table = 'booking_guest_details';
    
    protected $fillable = [
        'guest_type_id',
        'facility_booking_log_id',
        'facility_id',
        'quantity',
        'facility_quantity',
    ];
    
    public function guestType()
    {
        return $this->belongsTo(GuestType::class, 'guest_type_id');
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id');
    }
}
