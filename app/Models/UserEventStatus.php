<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserEventStatus extends Model
{
    use SoftDeletes;

    protected $fillable = ['name'];

    public function events() {
        return $this->hasMany('App\Models\UserEvent', 'user_event_status_id', 'id');
    }
}
