<?php

namespace App\Http\Middleware;

use App\Models\ApiConsumer;
use App\Models\ConsumerApiLog;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BaseAuthorizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the request has a valid token
        if ($request->bearerToken()) {
            // Attempt to authenticate the user with the token
            $user = Auth::user();

            if (! $user && Auth::guard('sanctum')->check()) {
                $user = Auth::guard('sanctum')->user();
                Auth::loginUsingId($user->id);
            }
            // If the user is authenticated, proceed with the request
            if ($user) {
                return $next($request);
            }
        } else {
            // $apiKey = $request->header('X-API-Key');
            // $apiCode = $request->header('x-api-code');
            // $consumer = ApiConsumer::where('api_code', $apiCode)->where('api_key', $apiKey)->first();

            // if ($consumer) {
            //     return $next($request);
            // }

            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
