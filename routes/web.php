<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\EmployeeLoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JobCardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Employee\EmployeeDashboardController;
use App\Http\Controllers\AjaxController;

// Root — render admin dashboard directly (auth is auto-bypassed for preview)
Route::get('/', [DashboardController::class, 'index'])->middleware(\App\Http\Middleware\AdminAuth::class);

// Admin Auth
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login',  [AdminLoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'login'])->name('login.post');
    Route::post('/logout',[AdminLoginController::class, 'logout'])->name('logout');

    // Protected admin routes
    Route::middleware(\App\Http\Middleware\AdminAuth::class)->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Job Cards — static routes BEFORE {jobCard} wildcard
        Route::get('/jobcards',                [JobCardController::class, 'index'])->name('jobcards.index');
        Route::get('/jobcards/create',         [JobCardController::class, 'create'])->name('jobcards.create');
        Route::post('/jobcards',               [JobCardController::class, 'store'])->name('jobcards.store');
        Route::get('/jobcards/track',          [JobCardController::class, 'track'])->name('jobcards.track');
        Route::get('/delivered-orders',        [JobCardController::class, 'deliveredIndex'])->name('jobcards.delivered');
        Route::get('/jobcards/{jobCard}/payment',  [JobCardController::class, 'payment'])->name('jobcards.payment');
        Route::post('/jobcards/{jobCard}/payment', [JobCardController::class, 'completePayment'])->name('jobcards.completePayment');
        Route::patch('/jobcards/{jobCard}/quick-status', [JobCardController::class, 'quickStatus'])->name('jobcards.quickStatus');
        Route::get('/receipts/{type}/{id}',        [JobCardController::class, 'receipt'])->name('jobcards.receipt');
        Route::get('/jobcards/{jobCard}',      [JobCardController::class, 'show'])->name('jobcards.show');
        Route::get('/jobcards/{jobCard}/edit', [JobCardController::class, 'edit'])->name('jobcards.edit');
        Route::put('/jobcards/{jobCard}',      [JobCardController::class, 'update'])->name('jobcards.update');
        Route::delete('/jobcards/{jobCard}',   [JobCardController::class, 'destroy'])->name('jobcards.destroy');

        // Employees
        Route::get('/employees',                 [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/create',          [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees',                [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{employee}',      [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{employee}',   [EmployeeController::class, 'destroy'])->name('employees.destroy');

        // Devices — flat sub-routes BEFORE {device} wildcard
        Route::get('/devices',                   [DeviceController::class, 'index'])->name('devices.index');
        Route::post('/devices',                  [DeviceController::class, 'storeDevice'])->name('devices.store');
        Route::post('/devices/brands',           [DeviceController::class, 'storeBrand'])->name('devices.brands.store');
        Route::delete('/devices/brands/{brand}', [DeviceController::class, 'destroyBrand'])->name('devices.brands.destroy');
        Route::post('/devices/faults',           [DeviceController::class, 'storeFault'])->name('devices.faults.store');
        Route::delete('/devices/faults/{fault}', [DeviceController::class, 'destroyFault'])->name('devices.faults.destroy');
        Route::delete('/devices/{device}',       [DeviceController::class, 'destroyDevice'])->name('devices.destroy');

        // Reports
        Route::get('/reports',            [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export/excel',[ReportController::class, 'exportExcel'])->name('reports.export.excel');
        Route::get('/reports/export/pdf',  [ReportController::class, 'exportPdf'])->name('reports.export.pdf');

        // Store Settings
        Route::get('/store',  [StoreController::class, 'edit'])->name('store.edit');
        Route::put('/store',  [StoreController::class, 'update'])->name('store.update');

        // Invoices
        Route::get('/invoices',                        [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/search',                 [InvoiceController::class, 'search'])->name('invoices.search');
        Route::get('/invoices/{jobCard}',              [InvoiceController::class, 'show'])->name('invoices.show');
        Route::put('/invoices/{jobCard}',              [InvoiceController::class, 'update'])->name('invoices.update');
        Route::patch('/invoices/{jobCard}/mark-paid',  [InvoiceController::class, 'markPaid'])->name('invoices.markPaid');

        // Notifications
        Route::get('/notifications',                             [NotificationController::class, 'index'])->name('notifications.index');
        Route::patch('/notifications/{jobCard}/payment',         [NotificationController::class, 'markPaymentReceived'])->name('notifications.payment');
        Route::patch('/notifications/{jobCard}/dismiss-assistant',[NotificationController::class, 'dismissAssistant'])->name('notifications.dismiss-assistant');
    });
});

// Employee Auth
Route::prefix('employee')->name('employee.')->group(function () {
    Route::get('/login',  [EmployeeLoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [EmployeeLoginController::class, 'login'])->name('login.post');
    Route::post('/logout',[EmployeeLoginController::class, 'logout'])->name('logout');

    Route::middleware(\App\Http\Middleware\EmployeeAuth::class)->group(function () {
        Route::get('/dashboard',                       [EmployeeDashboardController::class, 'index'])->name('dashboard');
        Route::get('/jobs',                            [EmployeeDashboardController::class, 'myJobs'])->name('jobs');
        Route::get('/jobs/{jobCard}/status',           [EmployeeDashboardController::class, 'updateStatus'])->name('jobs.status');
        Route::put('/jobs/{jobCard}/status',           [EmployeeDashboardController::class, 'saveStatus'])->name('jobs.status.save');
    });
});

// Ajax endpoints
Route::get('/ajax/brands', [AjaxController::class, 'brands'])->name('ajax.brands');
Route::get('/ajax/faults', [AjaxController::class, 'faults'])->name('ajax.faults');
