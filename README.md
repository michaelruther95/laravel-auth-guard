# Laravel Auth Guard
###### This Laravel package is created in order for developers to not create repetitive authentication algorithms. This package supports the following
- Basic user authentication
- Passport token refreshment (Note: This package relies on Passport package authentication)
- Password reset

###### This package only supports "Password Grant Client" authentication as of this moment.

###### Note: This package relies on Laravel Passport package. It must be installed first on your Laravel application before using this package.

---

#### Installation

Step #1: Install first and configure **Laravel Passport**.
composer require laravel/passport

Step #2: Install the package itself.
composer require michaelruther95/laravel-auth-guard

Step #3: Run migrations.
php artisan migrate

---

#### Usage

##### \Michaelruther95\LaravelAuthGuard\Services\Authenticator::authenticate($param1, $param2, $param3, $param4);
**Parameters:**
1. $param1 (Required) [STRING]: This parameter is the column on which will the authentication base to. (e.g: email, username, mobile_number).
2. $param2 (Required) [STRING]: This parameter is the value of the identifier.
3. $param3 (Required) [STRING]: This parameter is the password of the user you are trying to authenticate.
4. $param4 (Optional) [BOOLEAN]: This parameter if true will prepare cookies that contains your access token and refresh token. This usage is important especially if you want to store your tokens in an HTTPOnlyCookie. If the authentication is correct, the response of this method will have prepare_cookies property which you can set it alongside with the response.

---

##### \Michaelruther95\LaravelAuthGuard\Services\Authenticator::refreshtoken($param1, $param2)
**Parameters:**
1. $param1 (Required) [STRING]: This parameter is your current refresh token.
2. $param2 (Optional) [BOOLEAN]: This parameter if true will prepare cookies that contains your access token and refresh token. This usage is important especially if you want to store your tokens in an HTTPOnlyCookie. If the token refresh is successfully, the response of this method will have prepare_cookies property which you can set it alongside with the response.

---

##### \Michaelruther95\LaravelAuthGuard\Services\Authenticator::logout($param1)
**Parameters:**
1. $param (Required) [Request $req]: This parameter is the Request $req of your controller's method.

---

##### \Michaelruther95\LaravelAuthGuard\Services\PasswordReset::request($param1, $param2)
**Parameters:**
1. $param1 (Required) [STRING]: This parameter is the email of the user.
2. $param2 (Optional) [STRING]: This parameter is the time for the password reset request expiration. (e.g: "+1 hour", "+1 minute", "+1 day", "+1 year", "+1 month")

---

##### \Michaelruther95\LaravelAuthGuard\Services\PasswordReset::reset($param1, $param2)
**Parameters:**
1. $param1 (Required) [STRING]: This parameter is the password request reset token.
2. $param2 (Required) [STRING]: This parameter is the new password of the user.


