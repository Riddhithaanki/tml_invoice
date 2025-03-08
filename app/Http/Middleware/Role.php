<?php

namespace App\Http\Middleware;

use Orchid\Platform\Models\Role as RoleModel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, String $role): Response
    {
        $user = $request->user();
        if($user?->inRole(RoleModel::where('slug', $role)->first())) {
            return $next($request);
        }
        return abort(403);
    }
}
