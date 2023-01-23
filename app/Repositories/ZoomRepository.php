<?php

namespace App\Repositories;

use App\Helpers\HttpRequestHelper;
use Illuminate\Support\Facades\Log;
use App\Repositories\ThirdPartyRepositoryInterface;

class ZoomRepository implements ThirdPartyRepositoryInterface {

    /**
     * request to authorize the user
     * 
     * @return redirect to the authorization page
    */
    public static function userAuthorize()
    {
        return redirect(env('ZOOM_AUTH') . env('ZOOM_CLIENT_ID') . '&redirect_uri=' . env('ZOOM_REDIRECT_URL'));
    }

    /**
     * get user tokens (access_token and refresh_token)
     * 
     * @param $code
    */
    public static function getUserTokens($code)
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

        return [
            "token_type" => $response->token_type,
            "access_token" => $response->access_token,
            "refresh_token" => $response->refresh_token
        ];
    }

    /**
     * get user inforamtion from a third-party
     * 
     * @param $access_token
    */
    public function getUserInfo($access_token)
    {
        $url = env('ZOOM_API_V2') . "//users/me";
        $headers = [
            'Authorization' => "Bearer " . $access_token,
            'Content-Type' => "application/x-www-form-urlencoded"
        ];

        $response = HttpRequestHelper::sendRequest($url, "GET", [], [], $headers);
        // since token refresh corrupted let's redierct the user to re-authorize again
        if ($response->code === 124) {// token expired
            return $this->userAuthorize();
        }

        return $response;
    }

    /**
     * create an event from a third-party
     * 
     * @param $access_token
    */
    public function createUserEvent($access_token)
    {
        $url =  env('ZOOM_API_V2') . "/users/{userId}/meetings";
        $body = [
            "type" => 2,
            "duration" => 30,
            "topic" => "TOPIC",
            "timezone" => "EET",
            "agenda" => "agenda",
            "password" => 123456,
            "pre_schedule" => true,
            "default_password" => false,
            "end_times" => 1,
            "recurrence" => [
                "type" => 1
            ],
            "start_time" => "2023-1-22T12:00:00Z",
            "end_date_time" => "2023-1-22 12:30:00",
            "schedule_for" => "abdelrahman_elshorafa@outlook.com",
        ];

        $headers = [
            'Authorization' => "Bearer " . $access_token,
            'Content-Type' => "application/x-www-form-urlencoded"
        ];

        $response = HttpRequestHelper::sendRequest($url, "POST", [], [], $headers);
        // since token refresh corrupted let's redierct the user to re-authorize again
        if ($response->code === 124) {// token expired
            return $this->userAuthorize();
        }

        return $response;
    }
}