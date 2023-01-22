<?php

namespace App\Repositories;

interface ThirdPartyRepositoryInterface {

    /**
     * request to authorize the user
     * 
     * @return redirect to the authorization page
    */
    public static function userAuthorize();

    /**
     * get user tokens (access_token and refresh_token)
     * 
     * @param $code
    */
    public static function getUserTokens($code);

    /**
     * get user inforamtion from a third-party
     * 
     * @param $access_token
    */
    public function getUserInfo($access_token);

    /**
     * create an event from a third-party
     * 
     * @param $access_token
    */
    public function createUserEvent($access_token);
}