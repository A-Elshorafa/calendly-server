<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\ThirdPartyRepositoryInterface;

class ThirdParityController extends Controller
{
    protected $thirdPartyRepository;

    public function __construct(ThirdPartyRepositoryInterface $thirdPartyRepository)
    {
        $this->thirdPartyRepository = $thirdPartyRepository;
    }

    public function authorizeUser()
    {
        return $this->thirdPartyRepository->userAuthorize();
    }

    public function storeUserTokens(Request $request)
    {
        try {
            $code = $request->get('code');
            /**
             * it returns access_token, refresh_token and token_type  
            */
            $userTokens = $this->thirdPartyRepository->getUserTokens($code);

            $request->session()->put($userTokens);

            return response()->json(['success' => true, "message" => $request->session()->all()], 200);
        } catch (Exception $ex) {
            return response()->json(['success' => false, "message" => "server error"], 500);
        }
    }

    public function getUserInfo(Request $request)
    {
        // redirect user to authorize if the access token not found
        if (!$request->session()->has('access_token')) {
            return $this->thirdPartyRepository->userAuthorize();
        }

        return $this->thirdPartyRepository->getUserInfo($request->session()->get('access_token'));
    }

    public function createUserEvent(Request $request)
    {
        // redirect user to authorize if the access token not found
        if (!$request->session()->has('access_token')) {
            return $this->thirdPartyRepository->userAuthorize();
        }

        return $this->thirdPartyRepository->createUserEvent($request->session()->get('access_token'));
    }
}
