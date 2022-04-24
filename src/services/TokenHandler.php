<?php

namespace Michaelruther95\LaravelAuthGuard\Services;

use Str, Http, DB, URL;

class TokenHandler {

    public static function generate ($identifier, $password) {

        $oauthClient = DB::table('oauth_clients')
            ->where('password_client', 1)
            ->where('revoked', 0)
            ->first();
        
        if (!$oauthClient) {
            return [
                'success' => false,
                'error' => [
                    'code' => 'no_oauth_password',
                    'title' => 'No OAuth Password',
                    'message' => 'The system does not have a valid OAuth Password Setup.'
                ]
            ];
        }

        $response = Http::asForm()->post(URL::to('/') . '/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $oauthClient->id,
            'client_secret' => $oauthClient->secret,
            'username' => $identifier,
            'password' => $password,
            'scope' => '',
        ])->json();

        if (!$response['access_token']) {
            return [
                'success' => false,
                'error' => [
                    'code' => 'token_request_failed',
                    'title' => 'Access Token Request Failed',
                    'message' => 'Error Details: ' . json_encode($response)
                ]
            ]; 
        }

        return [
            'success' => true,
            'data' => $response
        ];
    }

    public static function refresh () {

    }

    public static function revoke ($type = 'access_token', $token) {

    }

    // public static function 

}