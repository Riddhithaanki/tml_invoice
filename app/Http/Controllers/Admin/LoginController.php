<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class LoginController extends Controller
{
    public function loginView()
    {
        return view('welcome');
    }
    public function loginAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            // Session::flash('error', $validator->errors()->first()); // Flash first error message
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::where('email', $request->email)
            ->where('roleId', 1)
            ->first();

        if (!$user) {
            return redirect()->back()->with('error', 'No admin account found with this email.');
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect()->route('dashboard')->with('success', 'Welcome to the Dashboard!');
        }

        return redirect()->back()->with('error', 'Invalid email or password.');

    }
    public function registerView()
    {
        return view('content.authentications.auth-register-basic');
    }
    public function registerAdmin(Request $request)
    {
        dd($request->all());
    }
}
