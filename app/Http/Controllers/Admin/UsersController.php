<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller
{
    public function index(){
        $users = User::latest('createdDtm')->get();
        return view('admin.pages.users.index', compact('users'));
    }

    public function getUsersData()
    {
        $users = User::with('role')->select('tbl_users.*');
        // dd($users);
        return DataTables::of($users)->make(true);
    }
}
