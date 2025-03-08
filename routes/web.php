<?php

use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\SystemLogsController;
use App\Http\Controllers\Admin\CollectionInvoicesController;
use App\Http\Controllers\Admin\DayworkInvoiceController;
use App\Http\Controllers\Admin\WaitingTimeInvoicesController;
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
Route::post('admin-login', [LoginController::class, 'loginAdmin'])->middleware(['log_activity'])->name('admin.login');

Route::middleware(['web', 'auth', 'admin', 'log_activity', 'network_error'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/invoices/data', [DashboardController::class, 'getInvoices'])->name('invoices.data');
    Route::get('invoice-data/{id}', [DashboardController::class, 'getInvoiceData'])->name('invoice.show');
    Route::get('/get-invoice-items', [DashboardController::class, 'getInvoiceItems'])->name('get.invoice.items');
    Route::post('/split-invoice', [DashboardController::class, 'splitInvoice'])->name('split.invoice');

    // User List Route

    Route::get('user-list', [UsersController::class, 'index'])->name('users.list');
    Route::get('users-data', [UsersController::class, 'getUsersData'])->name('users.data');

    //Collection Invoice
    Route::get('/collection-invoices-list/{type?}', [CollectionInvoicesController::class, 'index'])->name('collection.invoices.list');
    Route::get('/collection-invoices-details', [CollectionInvoicesController::class, 'details'])->name('collection.invoices.details');

    //Daywork Invoice
    Route::get('/daywork-invoices-list', [DayworkInvoiceController::class, 'index'])->name('daywork.invoices.list');

    //waitingtime Invoice
    Route::get('/waitingtime-invoices-list', [WaitingTimeInvoicesController::class, 'index'])->name('waitingtime.invoices.list');

    //System logs
    Route::get('systemlogs-list', [SystemLogsController::class, 'index'])->name('systemlogs.list');
    Route::get('systemlogs-details/{user_id}', [SystemLogsController::class, 'details'])->name('systemlogs.details');
});



Route::get('working-progress', function () {
    return view('workinprogress');
})->name('work');
