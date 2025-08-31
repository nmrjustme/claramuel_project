<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DayTourLogDetails extends Model
{
    protected $table = 'day_tour_log_details';  
    
    protected $fillable = [
        'user_id',
        'date_tour',
        'status',
        'approved_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
