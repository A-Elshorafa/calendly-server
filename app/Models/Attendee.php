<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'user_event_id'
    ];

    public function userEvent() {
        return $this->belongsTo('App\Models\UserEvent');
    }
}
