<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestAddons extends Model
{
    protected $table = 'guest_addons';
    
    protected $fillable = [
        'facility_booking_log_id',
        'type',
        'cost',
        'quantity',
        'total_cost'
    ];
}
