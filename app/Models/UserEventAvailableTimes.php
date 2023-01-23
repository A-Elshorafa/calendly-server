<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserEventAvailableTimes extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'available_time',
        'user_event_available_date_id'
    ];
}
