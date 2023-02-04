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
        'notes',
        'agenda',
        'user_id',
        'duration',
        'password',
        'expire_at',
        'is_notified',
        'is_subscribed',
        'subscribed_on',
        'calendly_link',
        'third_party_link',
        'third_party_name',
        'user_event_status_id',
    ];

    protected $with = ['availableDates.times'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'expire_at' => 'datetime',
        'subscribed_on' => 'datetime',
        'created_at' => 'datetime:d-m-Y H:i:s'
    ];


    public function host() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function attendee() {
        return $this->hasOne('App\Models\Attendee');
    }

    public function status() {
        return $this->belongsTo('App\Models\UserEventStatus', 'user_event_status_id', 'id');
    }

    public function availableDates() {
        return $this->hasMany('App\Models\UserEventAvailableDates', 'user_event_id', 'id');
    }

    public function availableTimes() {
        return $this->hasManyThrough('App\Models\UserEventAvailableTimes', 'App\Models\UserEventAvailableDates', 'user_event_id', 'user_event_available_date_id');
    }
}
