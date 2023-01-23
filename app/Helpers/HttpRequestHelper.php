<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class HttpRequestHelper
{
    public static function sendRequest($url, $method = 'GET', $params = [], $body = [], $headers = [])
    {
        $client = new Client();
        if ($method === 'GET') {
            $response = Http::withHeaders($headers)->get($url, $params);
            return json_decode($response->getBody()->getContents());
        } elseif ($method === 'POST') {
            $response = Http::withHeaders($headers)->post($url, $body);
            return json_decode($response->getBody()->getContents());
        }
    }
}
