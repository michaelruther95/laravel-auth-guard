<?php

namespace Michaelruther95\LaravelAuthGuard\Services;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;
use Str, Http, DB, URL;

class TokenHandler {

    public static function getOAuthClient () {
        $oauthClient = DB::table('oauth_clients')
            ->where('password_client', 1)
            ->where('revoked', 0)
            ->first();

        return $oauthClient;
    }

    public static function generate ($identifier, $password) {

        $oauthClient = self::getOAuthClient();
        
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

    public static function refresh ($refreshToken) {

        $oauthClient = self::getOAuthClient();

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
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $oauthClient->id,
            'client_secret' => $oauthClient->secret,
            'scope' => '',
        ])->json();

        if (isset($response['error'])) {
            return [
                'success' => false,
                'error' => [
                    'code' => 'refresh_token_request_error',
                    'title' => $response['hint'],
                    'message' => $response['error_description']
                ]
            ];
        }
         
        return [
            'success' => true,
            'data' => $response
        ];
    }

    public static function revoke ($tokenId) {

        $tokenRepository = app('Laravel\Passport\TokenRepository');
        $refreshTokenRepository = app('Laravel\Passport\RefreshTokenRepository');
        
        $tokenRepository->revokeAccessToken($tokenId);
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($tokenId);

        return true;

    }

}