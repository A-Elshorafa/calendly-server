<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ThirdParty;
use Illuminate\Http\Request;
use App\Models\UserThirdParty;
use App\Repositories\ThirdPartyRepositoryInterface;

class ThirdParityController extends Controller
{
    protected $thirdPartyRepository;

    public function __construct(ThirdPartyRepositoryInterface $thirdPartyRepository)
    {
        $this->thirdPartyRepository = $thirdPartyRepository;
    }

    /**
     * gets the users access-token and refresh-token, then save them on the DB
     * 
     * @param $request
     * @return Response
    */
    public function storeUserTokens(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'user_id' => 'required'
        ]);
        try {
            $code = $request->get('code');
            // returns access_token, refresh_token and token_type from third party 
            $userTokens = $this->thirdPartyRepository->getUserTokens($code);

            if(is_null($userTokens)) {
                return response()->json(['success' => false, "message" => "faild to get user tokens"], 500);
            }

            $userId = $request->get('user_id');
            $user = User::where('id', $userId)->first();
            if (!isset($user)) {
                return response()->json(['success' => false, "message" => "user not found"], 500);
            }
            // store access tokens on DB
            $tokensReference = $this->thirdPartyRepository->storeTokens($userId, $code, $userTokens);

            return response()->json(['success' => true, "message" => "access tokens saved successfully"], 200);
        } catch (Exception $ex) {
            return response()->json(['success' => false, "message" => "server error"], 500);
        }
    }    
}
