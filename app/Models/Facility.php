<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facility extends Model
{
    use HasFactory;
    
    protected $table = 'facilities'; 
    
    protected $fillable = [
        'name',
        'category',
        'description',
        'status',
        'pax',
        'bed_number',
        'room_number',
        'included',
        'rate_type',
        'price',
        'quantity'
    ];
    
    public function images () {
        return $this->hasMany(FacilityImage::class, 'fac_id');
    }
    
    public function bookings()
    {
        return $this->hasMany(FacilityBookingDetails::class, 'facility_id');
    }

    public function breakfasts()
    {
        return $this->hasMany(Breakfast::class);
    }
    
    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }
    
    public function discounts()
    {
        return $this->hasMany(FacilityDiscount::class, 'facility_id');
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class)
            ->using(AmenityFacility::class);
    }

    public function guestDetails(): HasMany
    {
        return $this->hasMany(BookingGuestDetails::class, 'facility_id');
    }
    
}

