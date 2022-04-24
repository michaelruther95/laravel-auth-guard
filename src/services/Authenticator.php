<?php

namespace Michaelruther95\LaravelAuthGuard\Services;
use Michaelruther95\LaravelAuthGuard\Services\TokenHandler;
use App\Models\User;
use Auth;

class Authenticator {


    public static function authenticate ($identifierColumn, $identifier, $password) {

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
            'token' => $token['data'],
            'will_expire_at' => date('Y-m-d H:i:s', strtotime($token['data']['expires_in'])),
            'expires_in' => $token['data']['expires_in']
        ];

    }

    public static function refreshtoken () {
        
        return [
            'message' => 'Refresh Token'
        ];

    }

    public static function destroytoken () {

        return [
            'message' => 'Destroy Token'
        ];

    }

}