<?php

namespace App\Repositories;

interface ThirdPartyRepositoryInterface {

    /**
     * return the authorization url
     * 
     * @return string
    */
    public static function userAuthorize();

    /**
     * get user tokens (access_token and refresh_token) from third party
     * 
     * @param $code
     * @return array
    */
    public function getUserTokens($code);

    /**
     * create an event on a third-party
     * 
     * @param $eventData
     * @return ?object
    */
    public function createUserEvent($eventData);

    /**
     * send creation request to the corresponding third-party
     * 
     * @param $eventData
     * @param $userAccessTokens
     *
     * @return object
    */
    public function sendCreateEventRequest($eventData, $userAccessTokens);

    /**
     * refresh access tokens then update access tokens on DB
     * 
     * @param $userId
     * @param $refreshToken
     * 
     * @return ?array
    */
    public function refreshAccessToken($userId, $refreshToken);

    /**
     * update access users' tokens on DB
     * 
     * @param $accessTokens
     * @param $userId
     * 
     * @return void
    */
    public function updateAccessTokens($accessTokens, $userId);

    
    /**
     * get user access-tokens from DB
     * 
     * @param $userId
     * @return ?array
    */
    public function getUserAccessTokens($userId);

    /**
     * store retrieved user tokens on the DB and returs a copy of them
     * 
     * @param $userId 
     * @param $code
     * @param $tokens
     * 
     * @return UserThirdParty
    */
    public function storeTokens($userId, $code, $tokens);
}