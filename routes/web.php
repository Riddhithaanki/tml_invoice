<?php

use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\DeliveryController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UsersController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return view('welcome');
})->name('admin.loginView');

Route::get('register', [LoginController::class, 'registerView'])->name('admin.registerView');
Route::post('admin-register', [LoginController::class, 'registerAdmin'])->name('admin.register');
// Route::get('login', [LoginController::class, 'loginAdminView'])->name('admin.loginView');
Route::post('admin-login', [LoginController::class, 'loginAdmin'])->name('admin.login');

Route::middleware(['web', 'auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/invoices/data', [DashboardController::class, 'getInvoices'])->name('invoices.data');
    Route::get('invoice-data/{id}', [DashboardController::class, 'getInvoiceData'])->name('invoice.show');
    Route::get('/get-invoice-items', [DashboardController::class, 'getInvoiceItems'])->name('get.invoice.items');
    Route::post('/split-invoice', [DashboardController::class, 'splitInvoice'])->name('split.invoice');

    // User List Route

    Route::get('user-list', [UsersController::class, 'index'])->name('users.list');
    Route::get('users-data', [UsersController::class, 'getUsersData'])->name('users.data');

    // Delivery Data

    Route::get('delivery-invoice-list',[DeliveryController::class,'index'])->name('delivery.index');
    Route::get('delivery-invoice-data',[DeliveryController::class,'getDeliveryInvoiceData'])->name('delivery.data');

    // Collection

    Route::get('collection-invoice-list',[CollectionController::class,'index'])->name('collection.index');
    Route::get('collection-invoice-data',[CollectionController::class,'getCollectionInvoiceData'])->name('collection.data');
});



Route::get('working-progress', function () {
    return view('workinprogress');
})->name('work');
