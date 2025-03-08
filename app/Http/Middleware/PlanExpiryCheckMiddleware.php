<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\PlanHelper;

class PlanExpiryCheckMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user && PlanHelper::isPlanExpiringSoon($user)) {
            // Set a session variable to indicate the plan is expiring soon
            session()->flash('plan_expiring', true);
        }

        return $next($request);
    }
}
