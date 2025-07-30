<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingGuestDetails extends Model
{
    protected $table = 'booking_guest_details';

    protected $fillable = [
        'guest_type_id',
        'facility_sumary_id',
        'quantity',
    ];
    
    public function guestType()
    {
        return $this->belongsTo(GuestType::class, 'guest_type_id');
    }

    public function facilitySummary()
    {
        return $this->belongsTo(FacilitySummary::class, 'facility_sumary_id');
    }
}
