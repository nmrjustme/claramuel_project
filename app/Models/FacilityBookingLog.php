<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FacilityBookingDetails;
use App\Models\Payments;

class FacilityBookingLog extends Model
{
    use HasFactory;
    protected $table = 'facility_booking_log';
    
    protected $fillable = [
        'user_id',
        'confirmation_token',
        'verified_at',
        'reference',
        'status',
        'code',
        'token',
        'confirmed_at',
        'payment_status',
        'is_read',
        'checked_in_at',
        'checked_in_by',
        'checked_out_at',
        'checked_out_by',
        'qr_code_path'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function details()
    {
        return $this->hasMany(FacilityBookingDetails::class, 'facility_booking_log_id');
    }
    
    public function bookingDetails()
    {
        return $this->hasMany(FacilityBookingDetails::class, 'facility_booking_log_id');
    }
    
    public function summaries()
    {
        return $this->hasMany(FacilitySummary::class, 'facility_booking_log_id');
    }
    
    public function payments()
    {
        return $this->hasMany(Payments::class, 'facility_log_id');
    }
    
    public function guestDetails()
    {
        return $this->hasMany(BookingGuestDetails::class, 'facility_booking_log_id');
    }

    public function guestAddons()
    {
        return $this->hasMany(GuestAddons::class, 'facility_booking_log_id');
    }

    
}   