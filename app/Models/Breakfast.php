<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Breakfast extends Model
{
    use HasFactory;
    protected $table = 'breakfast';
    public $timestamps = false;
    
    protected $fillable = [
        'facility_id',
        'price'
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function bookingDetails()
    {
        return $this->hasMany(FacilityBookingDetails::class);
    }
}