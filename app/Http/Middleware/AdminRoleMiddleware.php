<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminRoleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'User is not authenticated'], 401);
        }

        $user = Auth::user();
        // dd($user); // Check if user data is available

        if ($user->roleId !== 1) {
            return response()->json(['error' => 'Unauthorized user!'], 403);
        }

        return $next($request);
    }


}
