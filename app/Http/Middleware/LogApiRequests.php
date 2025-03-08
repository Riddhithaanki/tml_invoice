<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class LogApiRequests
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Check if the response is JSON
        if ($response->headers->get('Content-Type') === 'application/json') {
            $requestData = json_encode($request->all(), JSON_PRETTY_PRINT);
            $responseData = json_encode(json_decode($response->getContent()), JSON_PRETTY_PRINT);
            
            Log::channel('api')->info('API Request', [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'request' => $requestData,
                'response' => $responseData,
            ]);
        }

        return $response;
    }
}
