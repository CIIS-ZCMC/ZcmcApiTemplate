<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SessionValidation;
use App\Http\Middleware\Authorization;


/**
 * End point to authenticate user requirements session id
 * 
 * Done Mock Testing status : [PASSED]
 */
Route::namespace('App\Http\Controllers')->group(function () {
    Route::get('authenticate', 'AuthenticationController@authenticate');
});

Route::namespace('App\Http\Controllers')->group(function () {
    /**
     * Session validation to validate the cookie attach in the request.
     * Done Mock Testing status : [PASSED]
     */
    Route::middleware(SessionValidation::class)->group(function () {
        // END POINTS MUST BE ADDED HERE

        //Re authenticate user if cookie still valid also returning the user details
        Route::get('session-validation', 'AuthenticationController@validateSession');

        // With Authorization Layer [Middleware: Module-Code Permission]
        Route::middleware([Authorization::class.':M-001 view-all'])->group(function () {
            // End points with action permission must be put here
            // Route::get('end-point-here', 'controller@function');
        });
    });
    
    /**
     * Sign out End point
     * 
     * This will signout user from this server but not in umis server.
     */
    Route::get('signout', 'AuthenticationController@signOut');
});
