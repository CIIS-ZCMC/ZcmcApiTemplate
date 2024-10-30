<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

class SystemLogHelper
{   
    public static function infoLog($controller, $module, $message)
    {
        Log::channel('custom-info')->info($controller . ' Controller [' . $module . ']: message: ' . $message);
    }

    public static function errorLog($controller, $module, $errorMessage)
    {
        Log::channel('custom-error')->error($controller . ' Controller [' . $module . ']: message: ' . $errorMessage);
    }

}
