<?php

namespace App\Http\Middleware;

use App\Helpers\SystemLogHelper;
use App\Helpers\UtilitiesHelper;
use App\Models\UserDetails;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authorization
{
    // Optimized function to check if the module and action exist
    private function checkModuleAndAction($modules, $module, $action) {
        return array_reduce($modules, function($exists, $mod) use ($module, $action) {
            return $exists || ($mod->code === $module && in_array($action, $mod->permissions));
        }, false);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $routePermission): Response
    {
        list($module, $action) = explode(' ', $routePermission);

        $permissions = json_decode($request->permissions);

        $has_permissions = $this->checkModuleAndAction($permissions->modules, $module, $action);

        if(!$has_permissions){
            return response()->json([
                'message' => "Forbidden Access."
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
