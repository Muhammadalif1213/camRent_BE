<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $requiredRole): Response
    {

        $user = Auth::guard('api')->user();

        if (!$user || !$user->role || $user->role->name !== $requiredRole) {
            return response()->json([
                'message' => 'Unauthorized in ' . $role,
                'status_code' => 403,
                'data' => null
            ], 403);
        }
        return $next($request);
    }
}
