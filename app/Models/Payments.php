<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    protected $table = 'payments';
    public $timestamps = false;
    
    protected $fillable = [
        'facility_log_id',
        'event_log_id',
        'method',
        'status',
        'reference_no',
        'payer_email',
        'gcash_number',
        'amount',
        'receipt_path',
        'payment_date',
        'is_read',
        'is_updated',
        'rejection_reason',
        'verified_by',
        'verified_at',
        'verification_token',
        'qr_code_path'
    ];
    protected $casts = [
        'verified_at' => 'datetime',
    ];
    
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
    
    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }
    
    public function bookingLog()
    {
        return $this->belongsTo(FacilityBookingLog::class, 'facility_log_id');
    }
}
