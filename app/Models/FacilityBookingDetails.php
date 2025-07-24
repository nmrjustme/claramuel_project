<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityBookingDetails extends Model
{
    use HasFactory;
    public $timestamps = false;
    // In FacilityBookingDetails.php
    protected $casts = [
        'checkin_date' => 'datetime',
        'checkout_date' => 'datetime',
    ];
    protected $fillable = [
        'facility_booking_log_id',
        'facility_summary_id',
        'checkin_date',
        'checkout_date',
        'total_price',
    ];

    public function bookingLog()
    {
        return $this->belongsTo(FacilityBookingLog::class, 'facility_booking_log_id');
    }

    public function facilitySummary()
    {
        return $this->belongsTo(FacilitySummary::class, 'facility_summary_id');
    }

    public function breakfast()
    {
        return $this->belongsTo(Breakfast::class, 'breakfast_id');
    }

    // public function facility()
    // {
    //     return $this->belongsTo(Facility::class, 'facility_id');
    // }
    
}