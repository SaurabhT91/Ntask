<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the request has a token
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['error' => 'Unauthorized. Token is missing.'], 401);
        }

        // Add additional token validation logic here if needed

        return $next($request);
    }
}

