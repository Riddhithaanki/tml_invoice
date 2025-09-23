<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Debugging: Check if Laravel recognizes the user
        if (!auth()->check()) {
           // dd('Auth check failed! User is not authenticated.');
             return route('admin.loginView');
        } else {
            dd(auth()->user()); // Should show user data
        }

        if (!$request->expectsJson()) {
             return route('admin.loginView');
          //  return route('unauthorized_user');
        }
    }

}
