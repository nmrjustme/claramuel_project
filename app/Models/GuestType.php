<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestType extends Model
{
    protected $table = 'guest_type';
    
    protected $fillable = [
        'type',
        'rate'
    ];
}
