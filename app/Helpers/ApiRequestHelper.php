<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class ApiRequestHelper
{
    /**
     * Send a GET request with a custom token header.
     *
     * @param string $url The API endpoint.
     * @param string $token The token to set in the custom header.
     * @param string $headerName The name of the custom header.
     * @return Response
     */
    public static function getRequestWithToken(string $url, string $token, string $headerName = 'Umis-Test'): Response
    {
        return Http::withHeaders([
            $headerName => $token,
        ])->get($url);
    }

    /**
     * Send a POST request with a custom token header.
     *
     * @param string $url The API endpoint.
     * @param array $data The data to send in the request.
     * @param string $token The token to set in the custom header.
     * @param string $headerName The name of the custom header.
     * @return Response
     */
    public static function postRequestWithToken(string $url, array $data, string $token, string $headerName = 'Umis-Test'): Response
    {
        return Http::withHeaders([
            $headerName => $token,
        ])->post($url, $data);
    }
}
