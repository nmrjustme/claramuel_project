<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilitySummary extends Model
{
    use HasFactory;
    protected $table = 'facility_summary';
    public $timestamps = 'true';

    protected $fillable = [
        'facility_id',
        'breakfast_id',
        'facility_booking_log_id',
        'qty',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id');
    }

    public function bookingDetails()
    {
        return $this->hasMany(FacilityBookingDetails::class, 'facility_summary_id');
    }
    
    public function breakfast()
    {
        return $this->belongsTo(Breakfast::class);
    }
}