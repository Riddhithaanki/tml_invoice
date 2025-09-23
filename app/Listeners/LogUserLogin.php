<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;

class LogUserLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event)
   {
    try {
        
        $role = $event->user->role->role ?? 'Unknown Role';
        $email = $event->user->email ?? 'Unknown Email';
       
        DB::table('tbl_system_logs')->insert([
            'user_id' => $event->user->userId ?? $event->user->id,
            'activity' =>  $role . '-login',
            'details'   => json_encode([
                'new_data' => [
                    'email' => $email,
                   'ip'    => Request::ip(),
                ]
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info('Login event logged for user: ' . ($event->user->userId ?? $event->user->id));
    } catch (\Exception $e) {
        Log::error('System Log Insert Failed: ' . $e->getMessage());
    }
   }
}
