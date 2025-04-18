<?php

use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\DeliveryController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\SystemLogsController;
use App\Http\Controllers\Admin\CollectionInvoicesController;
use App\Http\Controllers\Admin\DayworkInvoiceController;
use App\Http\Controllers\Admin\HaulageInvoiceController;
use App\Http\Controllers\Admin\WaitingTimeInvoicesController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\InvoiceController;
use App\Http\Controllers\Customer\TicketController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FdController;
use App\Http\Controllers\Admin\SageController;

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
Route::post('admin-logout', [LoginController::class, 'logout'])->middleware(['log_activity'])->name('admin.logout');

Route::get('fetch-invoice', [SageController::class, 'getInvoices']);
Route::get('/invoice-items/{reference}', [SageController::class, 'getInvoiceItemsByReference']);
Route::get('/compare-invoice/{invoiceNumber}', [DashboardController::class, 'compareWithSage'])->name('compare.invoice');


<<<<<<< HEAD
Route::get('/php-check', function () {
    phpinfo();
});

Route::get('/check-sql-connection', [FdController::class, 'checkConnection']);
Route::middleware(['web', 'auth', 'admin' , 'log_activity', 'network_error'])->group(function () {
=======
Route::middleware(['web', 'auth', 'admin', 'log_activity', 'network_error'])->group(function () {
>>>>>>> 60c9585285d0c0a85d13ccd2f0bc6e870477c796
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/invoices/data', [DashboardController::class, 'getInvoices'])->name('invoices.data');
    Route::get('invoice-data/{id}', [DashboardController::class, 'getInvoiceData'])->name('invoice.show');
    Route::get('/get-invoice-items', [DashboardController::class, 'getInvoiceItems'])->name('get.invoice.items');
    Route::get('/get-split-invoice-items', [DashboardController::class, 'getSplitInvoiceItems'])->name('get.splitinvoice.items');
    Route::get('/get-merge-booking-items', [DashboardController::class, 'getMergeBookingItems'])->name('get.mergebookings.items');
    Route::post('/split-invoice', [DashboardController::class, 'splitInvoice'])->name('split.invoice');
    Route::post('/merge-booking', [DashboardController::class, 'mergeBooking'])->name('merge.booking');

    // Price Routes

    Route::post('/update-load-price', [InvoiceController::class, 'updateLoadPrice'])->name('update.invoice.price');
    Route::get('/invoice/price-history', [InvoiceController::class, 'getPriceHistory'])->name('get.price.history');

    // User List Route

    Route::get('user-list', [UsersController::class, 'index'])->name('users.list');
    Route::get('users-data', [UsersController::class, 'getUsersData'])->name('users.data');

    // Delivery Data
    Route::get('/delivery-invoice-list', [DeliveryController::class, 'index'])->name('delivery.index');
    Route::get('delivery-invoice-data', [DeliveryController::class, 'getDeliveryInvoiceData'])->name('delivery.data');

    // Collection
    Route::get('collection-invoice-list/{type?}', [CollectionController::class, 'index'])->name('collection.index');
    Route::get('collection-invoice-data', [CollectionController::class, 'getCollectionInvoiceData'])->name('collection.data');

    //Daywork Invoice
    Route::get('daywork-invoice-list', [DayworkInvoiceController::class, 'index'])->name('daywork.index');
    Route::get('daywork-invoice-data', [DayworkInvoiceController::class, 'getDayworkInvoiceData'])->name('daywork.data');

    //haulage Invoice
    Route::get('haulage-invoice-list', [HaulageInvoiceController::class, 'index'])->name('haulage.index');
    Route::get('haulage-invoice-data', [HaulageInvoiceController::class, 'getHaulageInvoiceData'])->name('haulage.data');

    //waitingtime Invoice
    Route::get('waitingtime-invoice-list', [WaitingTimeInvoicesController::class, 'index'])->name('waitingtime.index');
    Route::get('waitingtime-invoice-data', [WaitingTimeInvoicesController::class, 'getWaitingtimeInvoiceData'])->name('waitingtime.data');

    //System logs
    Route::get('systemlogs-list', [SystemLogsController::class, 'index'])->name('systemlogs.list');
    Route::get('systemlogs-details/{user_id}', [SystemLogsController::class, 'details'])->name('systemlogs.details');

    Route::post('/invoice/confirm', [DashboardController::class, 'confirm'])->name('invoice.confirm');
    Route::get('/invoice/{invoice}', [DashboardController::class, 'show'])->name('invoice.showData');

    // Invoice Differences Routes
    Route::get('/invoice-differences', [DashboardController::class, 'showDifferences'])
        ->name('invoice.differences');

    Route::post('/invoice-differences/{id}/update-status', [DashboardController::class, 'updateDifferenceStatus'])
        ->name('invoice.differences.update-status');
});

Route::group(['middleware' => ['web', 'auth', 'customer', 'log_activity', 'network_error'], 'prefix' => 'customer', 'as' => 'customer.'], function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

    Route::get('invoice-list/{type?}', [InvoiceController::class, 'index'])->name('invoice.index');
    Route::get('invoice-data', [InvoiceController::class, 'getInvoiceData'])->name('invoice.data');
    Route::get('pdf-list', [InvoiceController::class, 'pdflist'])->name('pdflist.index');


    Route::get('/invoice/tickets/{id}', [TicketController::class, 'showTickets'])->name('tickets.show');

    Route::post('/tickets/download', [TicketController::class,'downloadImages'])->name('tickets.download');
});

Route::get('working-progress', function () {
    return view('workinprogress');
})->name('work');

