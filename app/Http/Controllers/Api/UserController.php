<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $rules = [
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
            'mobile_number' => 'required|numeric',
            'age' => 'required|numeric',
        ];

        // Validate the request data
        $validationResponse = $this->validateData($request, $rules);
        if ($validationResponse) {
            return $validationResponse;
        }

        try {
            $user = new User();

            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->mobile_number = $request->mobile_number;
            $user->age = $request->age;
            $user->role_id = 2; // For User
            // $user->save();

            $imageUrl = $this->uploadImage($request, 'profile_image', 'profile_images');
            $user->profile_image = $imageUrl;
            $user->save();

            // Generate token and OTP
            $key = $user->id . '-' . now();
            $token = md5($key);
            // $otp = rand(100000, 999999);
            $otp = "123456";
            $otpData = Otp::create(['user_id' => $user->id, 'token' => $token, 'otp' => $otp]);

            $data = [
                'user' => $user,
                'otp' => $otpData,
            ];

            return $this->sendSuccessResponse($data, 'User registered successfully');
        } catch (Exception $e) {
            return $this->sendErrorResponse('Registration failed', 500, [$e->getMessage()]);
        }
    }

    public function verifyOtp(Request $request)
    {
        $rules = [
            'token' => 'required|string',
            'otp' => 'required|integer|digits:6'
        ];

        // Validate the request data
        $validationResponse = $this->validateData($request, $rules);
        if ($validationResponse) {
            return $validationResponse;
        }

        try {
            $attempt = Otp::where(['token' => $request['token'], 'otp' => $request['otp']])->first();
            if (is_null($attempt) || empty($attempt)) {
                return $this->sendErrorResponse('OTP is invalid/incorrect', 403, []);
            }

            $user = User::find($attempt['user_id']);

            $token = "Bearer " . $user->createToken('authenticated')->plainTextToken;

            return $this->sendSuccessResponse($token, 'OTP verified successfully');
        } catch (Exception $e) {
            return $this->sendErrorResponse('OTP verification failed', 500, [$e->getMessage()]);
        }
    }

    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ];

        // Validate the request data
        $validationResponse = $this->validateData($request, $rules);
        if ($validationResponse) {
            return $validationResponse;
        }

        try {
            $credentials = request(['email', 'password']);

            if (!Auth::attempt($credentials)) {
                return $this->sendErrorResponse('Unauthorized', 401, ['error' => 'Unauthorized']);
            }

            $user = Auth::user();
            $token = "Bearer " . $user->createToken('authenticated')->plainTextToken;

            $user->token = $token;
            return $this->sendSuccessResponse($user, 'User logged in successfully');
        } catch (Exception $e) {
            return $this->sendErrorResponse('Login failed', 500, [$e->getMessage()]);
        }
    }

    public function getProfile()
    {
        $id = Auth::id();

        $user = User::find($id);

        return $this->sendSuccessResponse($user, 'User profile fetched successfully');
    }

    public function logout()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->sendErrorResponse('No authenticated user found', 401, []);
            }
            $user->tokens()->delete();

            return $this->sendSuccessResponse([], 'User logged out successfully');
        } catch (Exception $e) {
            return $this->sendErrorResponse('Logout failed', 500, [$e->getMessage()]);
        }
    }

    public function changePassword(Request $request)
    {
        $rules = [
            'email' => 'required|string|email|max:255',
            'old_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string|min:8',
        ];

        // Validate the request data
        $validationResponse = $this->validateData($request, $rules);
        if ($validationResponse) {
            return $validationResponse;
        }

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->old_password, $user->password)) {
                return $this->sendErrorResponse('The provided credentials are incorrect.', 401, []);
            }

            $user->password = bcrypt($request->new_password);
            $user->save();

            return $this->sendSuccessResponse([], 'Password changed successfully');
        } catch (Exception $e) {
            return $this->sendErrorResponse('Password change failed', 500, [$e->getMessage()]);
        }
    }
}
