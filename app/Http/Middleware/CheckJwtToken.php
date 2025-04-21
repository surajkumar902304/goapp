<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckJwtToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Validate the JWT from the Authorization header
            $user = JWTAuth::parseToken()->authenticate();

            if (! $user) {
                return response()->json([
                    'message' => 'User not found'
                ], Response::HTTP_UNAUTHORIZED);
            }
        } catch (JWTException $e) {
            // If token is missing, expired, or invalid
            return response()->json([
                'message' => 'Token error: ' . $e->getMessage()
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Token is valid; proceed
        return $next($request);
    }
}
