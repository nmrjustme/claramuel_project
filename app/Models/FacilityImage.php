<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityImage extends Model
{
    protected $table = 'image_fac';
    protected $fillable = [
        'fac_id', 
        'image'
    ];
    
    protected $casts = [
        'order' => 'integer'
    ];
    
    public function facility()
    {
        return $this->belongsTo(Facility::class, 'fac_id');
    }
}