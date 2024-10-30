<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class UtilitiesHelper
{
    public static function encryptToken($token)
    {
        return openssl_encrypt($token, config('app.encrypt_decrypt_algorithm'), config('app.app_key'), 0, substr(md5(config('app.app_key')), 0, 16));
    }

    public static function decryptToken($encryptedToken)
    {
        return openssl_decrypt($encryptedToken->token, config('app.encrypt_decrypt_algorithm'), config('app.app_key'), 0, substr(md5(config('app.app_key')), 0, 16));
    }
}