<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserEventAvailableDates extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'date',
        'user_event_id'
    ];

    protected $casts = [
        "date" => "datetime"
    ];

    public function times() {
        return $this->hasMany('App\Models\UserEventAvailableTimes', 'user_event_available_date_id', 'id');
    }
}
