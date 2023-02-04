<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserThirdParty extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'token',
        'user_id',
        'access_token',
        'refresh_token',
        'third_party_id'
    ];
}
