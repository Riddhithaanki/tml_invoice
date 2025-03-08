<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;

class UserActivityLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Proceed with the request first
        $response = $next($request);

        // You can filter the request types here if you want to log only specific actions
        // For example, only log POST, PUT, PATCH, DELETE requests:
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $userId = Auth::check() ? Auth::id() : null;
            $activity = $request->path();
            // Optionally, add more details such as request data (be cautious with sensitive data)
            $details = json_encode($request->except(['password', 'password_confirmation', '_token']));

            SystemLog::create([
                'user_id'   => $userId,
                'activity'  => $activity,
                'details'   => $details,
            ]);
        }

        return $response;
    }
}
