<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CustomerRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'User is not authenticated'], 401);
        }

        $user = Auth::user();
        // dd($user); // Check if user data is available

        if (!in_array($user->roleId, [13])) {
            return response()->json(['error' => 'Unauthorized user!'], 403);
        }        

        return $next($request);
    }
}
