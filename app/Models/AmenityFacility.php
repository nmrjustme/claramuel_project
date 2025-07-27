<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AmenityFacility extends Pivot
{
    protected $table = 'amenity_facility';
    protected $fillable = [
        'facility_id',
        'amenity_id',
    ];
}
