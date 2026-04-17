<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingReplies extends Model
{
    protected $table = 'booking_replies';
    protected $fillable = [
        'booking_id', 
        'from_email',
        'from_name',
        'message',
        'is_read'
    ];
}
