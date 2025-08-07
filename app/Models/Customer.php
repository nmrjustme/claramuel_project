<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'phone',
        'confirmation_token',
        'email_verified_at',
    ];
}
