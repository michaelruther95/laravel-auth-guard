<?php

namespace Michaelruther95\LaravelAuthGuard\Services;

use Michaelruther95\LaravelAuthGuard\Services\TokenHandler;
use App\Models\User;
use Auth, DateTime, Cookie;

class Authenticator {


    public static function authenticate ($identifierColumn, $identifier, $password, $prepareHTTPOnlyCookiesForTokens = false) {

        $attempt = Auth::attempt([
            $identifierColumn => $identifier,
            'password' => $password
        ]);

        if (!$attempt) {
            return [
                'success' => false,
                'error' => [
                    'code' => 'invalid_credentials',
                    'title' => 'Invalid Credentials',
                    'message' => 'You have entered invalid credentials. Please try again.'
                ]
            ];
        }

        $token = TokenHandler::generate($identifier, $password);
        if (!$token['success']) {
            return [
                'success' => false,
                'error' => $token['error']
            ];
        }

        $preparedCookies = [
            'access_token' => null,
            'refresh_token' => null
        ];
        
        if ($prepareHTTPOnlyCookiesForTokens) {
            $expiry = (new DateTime('+' . ($token['data']['expires_in'] * 2) . ' seconds'))->getTimestamp();
            $preparedCookies['access_token'] = Cookie::make('access_token', $token['data']['access_token'], $expiry);
            $preparedCookies['refresh_token'] = Cookie::make('refresh_token', $token['data']['refresh_token'], $expiry);
        }

        $user = User::where($identifierColumn, $identifier)->first();

        return [
            'success' => true,
            'user' => $user,
            'token' => $token['data'],
            'prepared_cookies' => $preparedCookies
        ];

    }

    public static function refreshtoken ($refreshToken, $prepareHTTPOnlyCookiesForTokens = false) {

        $token = TokenHandler::refresh($refreshToken);

        if (!$token['success']) {
            return $token;
        }

        $preparedCookies = [
            'access_token' => null,
            'refresh_token' => null
        ];
        
        if ($prepareHTTPOnlyCookiesForTokens) {
            $expiry = (new DateTime('+' . ($token['data']['expires_in'] * 2) . ' seconds'))->getTimestamp();
            $preparedCookies['access_token'] = Cookie::make('access_token', $token['data']['access_token'], $expiry);
            $preparedCookies['refresh_token'] = Cookie::make('refresh_token', $token['data']['refresh_token'], $expiry);
        }

        return [
            'success' => true,
            'token' => $token['data'],
            'prepared_cookies' => $preparedCookies
        ];

    }

    public static function logout ($request, $prepareHTTPOnlyCookiesForTokens = false) {

        /**
         * Solution Source: https://laracasts.com/discuss/channels/laravel/after-revoking-the-token-in-laravel-passport-the-refresh-token-is-not-revoking
         */

        $tokenId = $request->user()->token()->id;
        $response = TokenHandler::revoke($tokenId);

        $preparedCookies = [
            'access_token' => null,
            'refresh_token' => null
        ];

        if ($prepareHTTPOnlyCookiesForTokens) {
            $expiry = (new DateTime('-1 hour'))->getTimestamp();
            $preparedCookies['access_token'] = Cookie::make('access_token', '', $expiry);
            $preparedCookies['refresh_token'] = Cookie::make('refresh_token', '', $expiry);
        }

        return [
            'success' => true,
            'prepared_cookies' => $preparedCookies
        ];

    }

}