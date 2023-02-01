<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getAuthUserInfo()
    {
        $user = Auth::user();
        return [
            "success" => isset($user),
            "data" => isset($user)? $user : null
        ];
    }
}
