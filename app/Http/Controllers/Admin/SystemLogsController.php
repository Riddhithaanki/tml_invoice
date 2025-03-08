<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserActivityLog;
use DB;

class SystemLogsController extends Controller
{
    //
    public function index()
    {
        //$logs = UserActivityLog::orderBy('created_at', 'desc')->get();
        $logs = DB::table('tbl_system_logs as s')
        ->join(DB::raw(
            '(SELECT user_id, MAX(created_at) as max_created_at 
              FROM tbl_system_logs 
              GROUP BY user_id) as latest'
        ), function ($join) {
            $join->on('s.user_id', '=', 'latest.user_id')
                 ->on('s.created_at', '=', 'latest.max_created_at');
        })
        ->leftJoin('tbl_users', 's.user_id', '=', 'tbl_users.userId')
        ->select('s.*', 'tbl_users.email', 'tbl_users.name')
        ->orderBy('s.created_at', 'desc')
        ->get();
    

        //session()->flash('network_error', 'A network error occurred. Please try again later.');
        return view('admin.pages.systemlogs.index', compact('logs'));
    }

    public function details($user_id)
    {   
        // $user_id = 1;
        // Retrieve logs for the given user_id (adjust the query as needed)
        $result['logs']= DB::table('tbl_system_logs')
            ->leftJoin('tbl_users', 'tbl_system_logs.user_id', '=', 'tbl_users.userId')
            ->select('tbl_system_logs.*', 'tbl_users.email', 'tbl_users.name')
            ->where('tbl_system_logs.user_id', $user_id)
            ->orderBy('tbl_system_logs.created_at', 'desc')
            ->get();

        return view('admin.pages.systemlogs.details',$result);
    }
}
