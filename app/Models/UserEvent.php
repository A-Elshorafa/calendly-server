<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserEvent extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'date',
        'user_id',
        'duration',
        'password',
        'calendly_link',
        'third_party_link',
        'third_party_name',
        'user_event_status_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime',
    ];


    public function host() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function attendees() {
        return $this->hasMany('App\Models\Attendee');
    }

    public function status() {
        return $this->belongsTo('App\Models\UserEventStatus', 'user_event_status_id', 'id');
    }
}