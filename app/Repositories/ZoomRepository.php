<?php

namespace App\Repositories;

use App\Models\ThirdParty;
use App\Models\UserThirdParty;
use App\Helpers\HttpRequestHelper;
use App\Repositories\ThirdPartyRepositoryInterface;

class ZoomRepository implements ThirdPartyRepositoryInterface {

    private static $thirdPartyId;

    public function __construct() {
        self::$thirdPartyId = ThirdParty::where('name', 'zoom')->first()->id;
    }
    /**
     * return the authorization url
     * 
     * @return string
    */
    public static function userAuthorize()
    {
        return env('ZOOM_AUTH') . env('ZOOM_CLIENT_ID') . '&redirect_uri=' . env('ZOOM_REDIRECT_URL');
    }

    /**
     * get user tokens (access_token and refresh_token) from third party
     * 
     * @param $code
     * @return array
    */
    public function getUserTokens($code)
    {
        $url = env('ZOOM_GET_AUTH_CODE') . '?grant_type=authorization_code&code=' . $code . "&redirect_uri=" . env('ZOOM_REDIRECT_URL');
        $headers = [
            'Content-Type' => "application/x-www-form-urlencoded",
            'Authorization' => "Basic " . base64_encode(env('ZOOM_CLIENT_ID').':'.env('ZOOM_CLIENT_SECRET'))
        ];
        $body = [
            "code"=> $code,
            "grant_type" => "authorization_code",
            "account_id"=> env('ZOOM_CLIENT_ID'),
            "redirect_uri"=> env('ZOOM_REDIRECT_URL'),
        ];

        // call http request
        $response = HttpRequestHelper::sendRequest($url, "POST", [], $body, $headers);

        if (isset($response) && property_exists($response, "error")) {
            return null;
        }

        return [
            "token_type" => $response->token_type,
            "access_token" => $response->access_token,
            "refresh_token" => $response->refresh_token
        ];
    }

    /**
     * create an event on a third-party
     * 
     * @param $eventData
     * @return ?object
    */
    public function createUserEvent($eventData)
    {
        $hostId = $eventData->host->id;
        
        $userAccessTokens = $this->getUserAccessTokens($hostId);
        if (is_null($userAccessTokens)) {
            return null;
        }

        $response = $this->sendCreateEventRequest($eventData, $userAccessTokens);
        // since token refresh corrupted let's redierct the user to re-authorize again
        if (is_object($response) && property_exists($response, 'code') && $response->code === 124) {// token expired
            $refreshedTokens = $this->refreshAccessToken($eventData->host->id, $userAccessTokens['refresh_token']);
            if (!is_null($refreshedTokens)) {
                // retry after tokens refreshed
                $response = $this->sendCreateEventRequest($eventData, $refreshedTokens);
            }
        }

        if (isset($response) && property_exists($response, 'start_url')) {
            return (object) [
                "password" => $response->password,
                "meeting_url" => $response->join_url
            ];
        }
        return null;
    }

    /**
     * send creation request to the corresponding third-party
     * 
     * @param $eventData
     * @param $userAccessTokens
     *
     * @return object
    */
    public function sendCreateEventRequest($eventData, $userAccessTokens)
    {
        $hostEmail = $eventData->host->email;
        $url =  env('ZOOM_API_V2') . "/users/$hostEmail/meetings";
        $body = [
            "type" => 2, // A scheduled meeting. 
            "duration" => $eventData->duration,
            "topic" => $eventData->name,
            "agenda" => $eventData->agenda,
            "password" => $eventData->password,
            "default_password" => false,
            "timezone" => 'UTC',//todo: $eventData->timeZone,
            "start_time" => $eventData->subscribed_on->toIso8601ZuluString(),
            "schedule_for" => $hostEmail,
            "meeting_invitees" => [
                (object)["email" => $eventData->attendee->email]
            ]
        ];

        $headers = [
            'Authorization' => "Bearer " . $userAccessTokens['access_token'],
            'Content-Type' => "application/json"
        ];

        return HttpRequestHelper::sendRequest($url, "POST", [], $body, $headers);
    }

    /**
     * refresh access tokens then update access tokens on DB
     * 
     * @param $userId
     * @param $refreshToken
     * 
     * @return ?array
    */
    public function refreshAccessToken($userId, $refreshToken)
    {
        $refresh_url = env('ZOOM_GET_AUTH_CODE') . "?grant_type=refresh_token&refresh_token=$refreshToken";
        $headers = [
            'Content-Type' => "application/x-www-form-urlencoded",
            'Authorization' => "Basic " . base64_encode(env('ZOOM_CLIENT_ID').':'.env('ZOOM_CLIENT_SECRET'))
        ];
        $response = HttpRequestHelper::sendRequest($refresh_url, "POST", [], [], $headers);
        if (is_object($response) && property_exists($response, 'access_token')) {
            $this->updateAccessTokens($response, $userId);
            return (array)$response;
        }
        return null;
    }

    /**
     * update access users' tokens on DB
     * 
     * @param $accessTokens
     * @param $userId
     * 
     * @return void
    */
    public function updateAccessTokens($accessTokens, $userId)
    {
        UserThirdParty::where('user_id', $userId)->where('third_party_id', self::$thirdPartyId)->update([
            "access_token" => $accessTokens->access_token,
            "refresh_token" => $accessTokens->refresh_token,
        ]);
    }

    /**
     * get user access-tokens from DB
     * 
     * @param $userId
     * @return ?array
    */
    public function getUserAccessTokens($userId)
    {
        $userAccessToken = null;
        $userThirdParty = UserThirdParty::where('user_id', $userId)->where('third_party_id', self::$thirdPartyId)->first();
        if (isset($userThirdParty)) {
            $userAccessToken = $userThirdParty->toArray();
        }

        return $userAccessToken;
    }

    /**
     * store retrieved user tokens on the DB and returs a copy of them
     * 
     * @param $userId 
     * @param $code
     * @param $tokens
     * 
     * @return UserThirdParty
    */
    public function storeTokens($userId, $code, $tokens)
    {
        $userThirdParty = UserThirdParty::where('user_id', $userId)->where('third_party_id', self::$thirdPartyId)->first();

        if (!empty($userThirdParty)) {
            $userThirdParty->access_token = $tokens['access_token'];
            $userThirdParty->refresh_token = $tokens['refresh_token'];
            $userThirdParty->save();
            $userThirdParty->fresh();
        } else {
            $userThirdParty = UserThirdParty::create([
                'token' => $code,
                'user_id' => $userId,
                'third_party_id' => self::$thirdPartyId,
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
            ]);
        }
        return $userThirdParty;
    }
}