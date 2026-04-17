<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestType extends Model
{
    protected $table = 'guest_type';
    
    protected $fillable = [
        'type',
        'location',
        'rate'
    ];

    public function bookingGuestDetails()
    {
        return $this->hasMany(BookingGuestDetails::class, 'guest_type_id');
    }
}
