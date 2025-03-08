<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class LogRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Log the request
        Log::channel('daily')->info('Request:', [
            'method' => $request->getMethod(),
            'url' => $request->fullUrl(),
            'headers' => $request->headers->all(),
            'body' => $request->all()
        ]);

        $response = $next($request);

        // Log the response
        Log::channel('daily')->info('Response:', [
            'status' => $response->status(),
            'headers' => $response->headers->all(),
            'body' => $response->getContent()
        ]);

        return $response;
    }

    /**
     * Handle an exception that occurred during the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return void
     */
    public function reportException(Request $request, \Exception $exception)
    {
        Log::channel('daily')->error('Exception:', [
            'message' => $exception->getMessage(),
            'stack' => $exception->getTraceAsString()
        ]);
    }
}
