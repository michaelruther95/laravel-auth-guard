<?php

namespace Michaelruther95\LaravelAuthGuard\Services;

use Michaelruther95\LaravelAuthGuard\Services\TokenHandler;
use App\Models\User;
use Auth;

class Authenticator {


    public static function authenticate ($identifierColumn, $identifier, $password, $saveToHTTPOnlyCookie = false) {

        $attempt = Auth::attempt([
            $identifierColumn => $identifier,
            'password' => $password
        ]);

        if (!$attempt) {
            return [
                'authenticated' => false,
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
                'authenticated' => false,
                'error' => $token['error']
            ];
        }

        $user = User::where($identifierColumn, $identifier)->first();
        return [
            'user' => $user,
            'token' => $token['data']
        ];

    }

    public static function refreshtoken ($refreshToken, $saveToHTTPOnlyCookie = false) {

        $token = TokenHandler::refresh($refreshToken);
        return $token;

    }

    public static function logout ($request) {

        /**
         * Solution Source: https://laracasts.com/discuss/channels/laravel/after-revoking-the-token-in-laravel-passport-the-refresh-token-is-not-revoking
         */

        $tokenId = $request->user()->token()->id;
        $response = TokenHandler::revoke($tokenId);

        return [
            'success' => true
        ];

    }

}