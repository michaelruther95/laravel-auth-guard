<?php

namespace Michaelruther95\LaravelAuthGuard\Services;
use App\Models\User;
use DB, Str;

class PasswordReset {

    public static function request ($email, $timeToAdd = null) {

        $user = User::where('email', $email)->first();
        if (!$user) {
            return [
                'success' => false,
                'error' => [
                    'code' => 'user_email_not_found',
                    'title' => 'User Email Not Found',
                    'message' => 'The user email you have provided does not exist.'
                ]
            ];
        }

        $dateExpiration = $timeToAdd ? date("Y-m-d H:i:s", strtotime($timeToAdd)) : null;
        $currentDate = date('Y-m-d H:i:s');
        if ($dateExpiration) {
            if ($dateExpiration <= $currentDate) {
                return [
                    'success' => false,
                    'error' => [
                        'code' => 'invalid_expiration_date',
                        'title' => 'Invalid Expiration Date',
                        'message' => 'The expiration you have provided is lesser than the current date.'
                    ]
                ];
            }
        }
        

        $resetToken = strtotime("now") . '-' . Str::random(20);

        DB::table('password_resets')
            ->where('email', $email)
            ->delete();
        
        $record = DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $resetToken,
            'created_at' => $currentDate,
            'token_expiration' => $dateExpiration
        ]);

        return [
            'success' => true,
            'data' => $record
        ];

    }

    public static function reset ($resetToken, $password) {
        
        $record = DB::table('password_resets')
            ->where('token', $resetToken)
            ->first();

        if (!$record) {
            return [
                'success' => false,
                'error' => [
                    'code' => 'invalid_password_reset_token',
                    'title' => 'Invalid Password Reset Token',
                    'message' => 'The password reset token you have provided is invalid'
                ]
            ];
        }

        $currentDate = date('Y-m-d H:i:s');
        if ($record->token_expiration) {
            if ($record->token_expiration <= $currentDate) {
                return [
                    'success' => false,
                    'error' => [
                        'code' => 'reset_password_token_expired',
                        'title' => 'Reset Password Token Expired',
                        'message' => 'The reset password token you have provided is expired.'
                    ]
                ];
            }
        }

        $user = User::where('email', $record->email)
            ->first();

        if (!$user) {
            DB::table('password_resets')
                ->where('email', $record->email)
                ->delete();

            return [
                'success' => false,
                'error' => [
                    'code' => 'user_does_not_exist',
                    'title' => 'User Does Not Exist',
                    'message' => 'The user you are trying to reset the password does not exist.'
                ]
            ];
        }

        $user->password = bcrypt($password);
        $user->save();

        DB::table('password_resets')
            ->where('email', $record->email)
            ->delete();

        return [
            'success' => true
        ];

    }

}