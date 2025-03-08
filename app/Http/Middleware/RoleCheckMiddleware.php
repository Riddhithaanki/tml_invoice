<?php

namespace App\Http\Middleware;

use App\Models\Plan;
use App\Models\SubscribedUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$allowedRoles): Response
    {
        $user = $request->user();

        // Check if the user is authenticated and has one of the allowed roles
        if ($user) {
            // Check if the user has an active plan
            $demo = $this->isPlanActive($user);
            if ($this->isPlanActive($user)) {
                return $next($request);
            } else {
                return redirect()->route('planDetails');
            }
        }

        return abort(403);
    }

    private function isPlanActive($user)
    {
        if ($user->plan_id) {
            $plan_data = SubscribedUser::where('plan_id',$user->plan_id)->where('user_id',$user->id)->latest()->first();
            // dd($plan_data);
            $plan = Plan::find($user->plan_id);
            // dd($plan);
            if ($plan_data) {
                $planEndDate = $plan_data->created_at->addDays($plan->days);
                // dd($planEndDate);
                // Compare the plan end date with the current date
                return now()->lt($planEndDate);
            }
        }

        return false;
    }

}
