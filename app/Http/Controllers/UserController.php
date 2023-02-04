<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ThirdParty;
use App\Models\UserThirdParty;
use App\Repositories\ZoomRepository;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * returns authenticatd user information
     * 
     * @param Request
     * @return Response
    */
    public function getAuthUserInfo(Request $request)
    {
        $user = Auth::user();
        // return third_parity authorization link in case user not has access_token
        // if server returns `authorization_link` this will alert the user to get authorized on zoom 
        if (isset($user)) {
            $thirdParty = ThirdParty::where('name', 'zoom')->first();
            $userThirdParty = UserThirdParty::where('user_id', $user->id)->where('third_party_id', $thirdParty->id)->first();
            if (!isset($userThirdParty)) {
                // get zoom authorization link
                // todo: get users integrations statuses in a separate endpoints
                $user['authorization_link'] = ZoomRepository::userAuthorize();
            }
        }

        return [
            "success" => isset($user),
            "data" => isset($user)? $user : null
        ];
    }
}
