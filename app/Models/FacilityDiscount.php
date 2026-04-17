<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityDiscount extends Model
{
    protected $table = 'facility_discounts';
    
    protected $fillable = [
        'facility_id',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
    ];
    
    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id');
    }
}
