<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use function PHPSTORM_META\map;

class Order extends Model
{
    protected $table = 'orders';
    
    protected $fillable = [
        'reference_number',
        'amount',
        'status',
        'payment_id',
        'failure_reason',
        'paid_at',
        'payment_data',
        'token',
    ];
}
