<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Crypt;

class UsersController extends Controller
{
    public function index(){
        $users = User::latest('createdDtm')->get();
        return view('admin.pages.users.index', compact('users'));
    }

    public function getUsersData()
    {
        $users = User::with('role')->select('tbl_users.*')->get();
       
        return DataTables::of($users)
            ->addColumn('action', function ($user) {
                return '<a href="' . route('users.edit', [Crypt::encrypt($user->userId)]) . '" class="btn btn-xs btn-primary">View</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
     }

     public function edit($id)
     {
         $userId = Crypt::decrypt($id);
         
         $user = User::findOrFail($userId);
         return view('admin.pages.users.edit', compact('user'));
     }


     public function updateUser(Request $request, $id)
{
    // Find user by ID
    $user = User::findOrFail($id);

    // Update user fields
    $user->name       = $request->input('name');
    $user->email      = $request->input('email');
    $user->mobile     = $request->input('mobile');
    $user->roleId     = $request->input('role');
    $user->updatedBy  = \Auth::id(); // Or set manually like $user->updatedBy = 1;

    // Optionally update timestamp
    $user->updatedDtm = now();
   
    // Save changes
    $user->save();

     return redirect()->route('users.list')->with('success', 'User updated successfully!');
}
}
