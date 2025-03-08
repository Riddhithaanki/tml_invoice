<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\LogRequests;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware([LogRequests::class])->group(function () {
    Route::post('/register-user', [UserController::class, 'register']);
    Route::post('verify-otp', [UserController::class, 'verifyOtp']);

    Route::get('unauthorized_user', function () {
        $response['status'] = 'error';
        $response['code'] = 403;
        $response['message'] = 'unauthorized user!';
        return response()->json($response);
    })->name('unauthorized_user');

    Route::post('login-user', [UserController::class, 'login']);
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('logout-user', [UserController::class, 'logout']);

    // Autheticated Routes Here
    //User Route
    Route::get('/user-profile', [UserController::class, 'getProfile']);
    Route::post('/change-password', [UserController::class, 'changePassword']);

    
});
