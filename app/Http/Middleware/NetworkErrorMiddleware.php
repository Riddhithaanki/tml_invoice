<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NetworkErrorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (\Exception $e) {
            if ($this->isNetworkError($e)) {
                session()->flash('network_error', 'A network error occurred. Please try again later.');
                return redirect()->back();
            }
            throw $e;
        }
    }

    protected function isNetworkError(\Exception $e)
    {
        // Check if the exception message contains "network"
        return stripos($e->getMessage(), 'network') !== false;
    }
}
