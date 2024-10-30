<?php

namespace App\Http\Controllers;

use App\Helpers\ApiRequestHelper;
use App\Helpers\SystemLogHelper;
use App\Helpers\UtilitiesHelper;
use App\Models\UserDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;

class AuthenticationController extends Controller
{
    /**
     * Summary of authenticate from UMIS
     * 
     * Authenticate the user by passing the session ID for user verification
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function authenticate(Request $request)
    {
        /**
         * Use this to delay the authentication so that you can use Developer Tool in the client
         * First uncomment this then this will prevent redirect to pr monitoring client open the DevTools F12
         * then uncomment this refresh the client.
         */
        // return response()->json(['message' => "TEST"], 500); 

        // Retrieve session_id on request
        $session_id = $request->query("session_id");

        $params = ["session_id" => $session_id];

        // Building url verification to umis with the given session id
        $url = env("UMIS_DOMAIN")."/api/authenticate-user-session";
        $url .= '?'.http_build_query($params);


        SystemLogHelper::errorLog("AuthenticationController", "authenticate", $url);

        // Initiate request to verify authenticity of session_id
        $response = ApiRequestHelper::getRequestWithToken($url, env("API_KEY"), env("UMIS_API_HEADER"));

        // Invalid session id will be un-authorized.
        if(!$response->successful()){
            $statusCode = $response->status(); // Get the status code from the response
            $responseData = $response->json(); // Decode the response JSON if needed

            // You can customize the message based on the response from UMIS
            $message = isset($responseData['message']) ? $responseData['message'] : "Unauthorized request rejected by UMIS.";

            return response()->json(['message' => $message], $statusCode);
        }

        $responseData = $response->json(); 

        /**
         * Succcess response
         * 
         * Retrieve session data this is user session that will retrieve only the token and expiration date.
         * Retrieve user information from umis ensure no redundant record of user details.
         * Retrieve permissions this is user permission intended for this system.
         */
        $session = $responseData['session'] ?? null;
        $user = $responseData['user_details'] ?? null;
        $permissions = $responseData['permissions'] ?? null;

        $user_already_signined = UserDetails::where('token', $session['token'])->first();

        if(!$user_already_signined){
            // Store needed details to prevent signin on page leave. This record will be available only until token expire
            UserDetails::create([
                "user_details" => json_encode($user),
                "permissions" => json_encode($permissions),
                "token" => $session['token'],
                "token_exp" => $session['token_exp'],
            ]);
        }else{
            $tokenExpTime = Carbon::parse($session['token_exp']);
            $isTokenExpired = $tokenExpTime->isPast();

            if(!$isTokenExpired){
                $user_already_signined->delete();
                return response()->json(['message' => "unauthorized token expired"],Response::HTTP_UNAUTHORIZED);
            }
        }

        /**
         * Encrypt the cookie this type of layer protection is not necessary
         * and might be remove in future if no unknown type of attacks happens during
         * the early stage of the system.
         */
        $encryptToken = UtilitiesHelper::encryptToken($session['token']);

        // Attach the cookie in response with target session domain
        return response()->json([
            'user' => $user
        ])->cookie(config('app.cookie_name'), json_encode(['token' => $encryptToken]), 60, '/', config('app.session_domain'), false);
    }

    /**
     * Summary of validateSession
     * 
     * This function is intended only to return the user details 
     * but this will be use the basis for re-authenticating user
     * in senario that the page has been refresh
     * 
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function validateSession(Request $request)
    {
        return response()->json(["user" => json_decode($request->user)], Response::HTTP_OK);
    }
}
