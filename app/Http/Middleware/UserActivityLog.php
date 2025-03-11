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

        // Log only specific actions (POST, PUT, PATCH, DELETE)
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $userId = Auth::check() ? Auth::id() : null;
            $activity = $request->path();

            // Initialize details array
            $details = [];

            // Handle UPDATE operations (PUT, PATCH)
            if (in_array($request->method(), ['PUT', 'PATCH'])) {
                // Get the model ID from the request (assuming it's passed in the URL)
                $modelId = $request->route('id'); // Adjust based on your route parameter

                // Fetch the old data from the database
                $tableName = $this->getTableNameFromRequest($request); // Helper method to get table name
                $oldData = DB::table($tableName)->where('id', $modelId)->first();

                if ($oldData) {
                    // Convert old data to array and exclude sensitive fields
                    $oldData = collect($oldData)->except(['password', 'created_at', 'updated_at'])->toArray();
                    $details['old_data'] = $oldData;
                }

                // Get new data from the request (excluding sensitive fields)
                $newData = $request->except(['password', 'password_confirmation', '_token']);
                $details['new_data'] = $newData;
            }

            // Handle CREATE operations (POST)
            if ($request->method() === 'POST') {
                // Get new data from the request (excluding sensitive fields)
                $newData = $request->except(['password', 'password_confirmation', '_token']);
                $details['new_data'] = $newData;
            }

            // Log the activity
            SystemLog::create([
                'user_id'   => $userId,
                'activity'  => $activity,
                'details'   => json_encode($details), // Store details as JSON
            ]);
        }

        return $response;
    }
}
