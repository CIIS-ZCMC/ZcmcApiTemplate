<?php

namespace App\Http\Middleware;

use App\Helpers\UtilitiesHelper;
use App\Models\UserDetails;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionValidation
{
    /**
     * Handle an incoming request.
     * 
     * Validate user session to prevent unauthorized access
     * in this system.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Retrieve the cookie in the request
        $cookie = $request->cookie(config('app.cookie_name'));

        // In case the value of cookie is array
        if (is_array($cookie)) {
            $cookie = $cookie[config("app.cookie_name")];
        }

        /**
         * If cookie value is null though the request has a cookie this will remove the cookie it request
         * ensure that valid cookie will only allow to process the request.
         */
        if (!$cookie) {
            return response()->json([
                'message' => 'unauthorized invalid cookie value.'
            ], Response::HTTP_UNAUTHORIZED)->cookie(config("app.cookie_name"), '', -1);
        }
        
        $encryptedToken = json_decode($cookie);

        /**
         * Decrypt the cookie using openssl, encryption and decryption algorithm and app key.
         */
        $token = UtilitiesHelper::decryptToken($encryptedToken);

        /**
         * Validate if the user has current session registered in this system
         * session will come from the umis system.
         */
        $user = UserDetails::where('token', $token)->first();

        if(!$user){
            return response()->json([
                'message' => 'unauthorized no session registered.'
            ], Response::HTTP_UNAUTHORIZED)->cookie(config("app.cookie_name"), '', -1);
        }
        
        $user->update(['token_exp' => Carbon::now()->addMinutes(30)]);

        /**
         * Merge requester user details in request so that controller has copy of the user information.
         */
        $request->merge(['user' => $user->user_details]);

        /**
         * Merge requester user permissions in request so that controller has copy of the user rights.
         */
        $request->merge(['permissions' => $user->permissions]);

        
        $tokenExpTime = Carbon::parse($user->token_exp);
        $isTokenExpired = $tokenExpTime->isPast();

        if ($isTokenExpired) {
            return response()->json(['error' => 'Session expired.'], Response::HTTP_UNAUTHORIZED);
        }

        // Check if the token will expire in 5 minutes
        $fiveMinutesFromNow = Carbon::now()->addMinutes(5);
        $shouldExtendExpiration = $tokenExpTime->lessThanOrEqualTo($fiveMinutesFromNow);

        /**
         * @next Process the next request, which will validate authorization, 
         * then to the controller if the user is authorized. The response of the controller 
         * will be received by the authorization middleware, then it will return the response
         * where this middleware will receive it and store in variable $response.
         */
        $response = $next($request);

        /**
         *  Extend expiration only when the cookie will about to expire in 5 mins 
         * To prevent unnecessary process
         */
        if ($shouldExtendExpiration) {
            return $response->cookie(
                config('app.cookie_name'), // The cookie name
                $cookie, // Encrypted token as the value
                30, // Extend for another 30 minutes
                '/', // Path
                config('app.session_domain'), // Domain from config
                false, // HTTPS secure flag
                true // HttpOnly flag
            );
        }
        

        // Return response without updating the cookie expiration
        return $response;
    }
}
