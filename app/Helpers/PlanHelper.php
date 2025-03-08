<?php

namespace App\Helpers;

use App\Models\Plan;
use App\Models\SubscribedUser;

class PlanHelper
{
    public static function isPlanActive($user)
    {
        if ($user->plan_id) {
            $plan_data = SubscribedUser::where('plan_id', $user->plan_id)
                ->where('user_id', $user->id)
                ->latest()
                ->first();

            $plan = Plan::find($user->plan_id);

            if ($plan_data) {
                $planEndDate = $plan_data->created_at->addDays($plan->days);
                return now()->lt($planEndDate);
            }
        }

        return false;
    }

    public static function isPlanExpiringSoon($user, $daysBefore = 6)
    {
        if ($user->plan_id) {
            $plan_data = SubscribedUser::where('plan_id', $user->plan_id)
                ->where('user_id', $user->id)
                ->latest()
                ->first();

            $plan = Plan::find($user->plan_id);

            if ($plan_data) {
                $planEndDate = $plan_data->created_at->addDays($plan->days);
                return now()->diffInDays($planEndDate, false) <= $daysBefore && now()->lt($planEndDate);
            }
        }

        return false;
    }
}
