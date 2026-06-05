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
use App\Http\Controllers\Admin\FieldComplaintController;
use App\Http\Controllers\Admin\ServiceTypeController;
use App\Http\Controllers\Admin\SmsSettingsController;
use App\Http\Controllers\Admin\LabelSettingsController;
use App\Http\Controllers\Admin\WhatsappSettingsController;
use App\Http\Controllers\SuperAdmin\AuthController as SAAuth;
use App\Http\Controllers\SuperAdmin\DashboardController as SADash;
use App\Http\Controllers\SuperAdmin\ShopController as SAShop;

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
        Route::post('/devices/faults',                    [DeviceController::class, 'storeFault'])->name('devices.faults.store');
        Route::delete('/devices/faults/{fault}',          [DeviceController::class, 'destroyFault'])->name('devices.faults.destroy');
        Route::get('/devices/accessories',                [DeviceController::class, 'indexAccessories'])->name('devices.accessories.index');
        Route::post('/devices/accessories',               [DeviceController::class, 'storeAccessory'])->name('devices.accessories.store');
        Route::patch('/devices/accessories/{accessory}', [DeviceController::class, 'updateAccessory'])->name('devices.accessories.update');
        Route::delete('/devices/accessories/{accessory}', [DeviceController::class, 'destroyAccessory'])->name('devices.accessories.destroy');
        Route::delete('/devices/{device}',                [DeviceController::class, 'destroyDevice'])->name('devices.destroy');

        // Reports
        Route::get('/reports',            [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export/excel',[ReportController::class, 'exportExcel'])->name('reports.export.excel');
        Route::get('/reports/export/pdf',  [ReportController::class, 'exportPdf'])->name('reports.export.pdf');

        // Store Settings
        Route::get('/store',  [StoreController::class, 'edit'])->name('store.edit');
        Route::put('/store',  [StoreController::class, 'update'])->name('store.update');

        // SMS Settings
        Route::get('/sms-settings',         [SmsSettingsController::class, 'edit'])->name('sms-settings.edit');
        Route::put('/sms-settings',         [SmsSettingsController::class, 'update'])->name('sms-settings.update');
        Route::post('/sms-settings/test',   [SmsSettingsController::class, 'test'])->name('sms-settings.test');

        // Label Settings
        Route::get('/label-settings',  [LabelSettingsController::class, 'edit'])->name('label-settings.edit');
        Route::put('/label-settings',  [LabelSettingsController::class, 'update'])->name('label-settings.update');

        // WhatsApp Settings
        Route::get('/whatsapp-settings',       [WhatsappSettingsController::class, 'edit'])->name('whatsapp-settings.edit');
        Route::put('/whatsapp-settings',       [WhatsappSettingsController::class, 'update'])->name('whatsapp-settings.update');
        Route::post('/whatsapp-settings/test', [WhatsappSettingsController::class, 'test'])->name('whatsapp-settings.test');
        Route::post('/jobcards/{jobCard}/send-quotation', [JobCardController::class, 'sendQuotation'])->name('jobcards.send-quotation');
        Route::post('/jobcards/{jobCard}/send-uncollected-reminder', [JobCardController::class, 'sendUncollectedReminder'])->name('jobcards.send-uncollected-reminder');

        // Invoices
        Route::get('/invoices',                        [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/search',                 [InvoiceController::class, 'search'])->name('invoices.search');
        Route::get('/invoices/{jobCard}',              [InvoiceController::class, 'show'])->name('invoices.show');
        Route::put('/invoices/{jobCard}',              [InvoiceController::class, 'update'])->name('invoices.update');
        Route::patch('/invoices/{jobCard}/mark-paid',  [InvoiceController::class, 'markPaid'])->name('invoices.markPaid');

        // Field Complaints (Field Services)
        Route::get('/field-complaints',                           [FieldComplaintController::class, 'index'])->name('field-complaints.index');
        Route::get('/field-complaints/create',                    [FieldComplaintController::class, 'create'])->name('field-complaints.create');
        Route::post('/field-complaints',                          [FieldComplaintController::class, 'store'])->name('field-complaints.store');
        Route::get('/field-complaints/{fieldComplaint}/invoice',  [FieldComplaintController::class, 'invoice'])->name('field-complaints.invoice');
        Route::get('/field-complaints/{fieldComplaint}',          [FieldComplaintController::class, 'show'])->name('field-complaints.show');
        Route::put('/field-complaints/{fieldComplaint}',          [FieldComplaintController::class, 'update'])->name('field-complaints.update');
        Route::patch('/field-complaints/{fieldComplaint}/assign', [FieldComplaintController::class, 'assign'])->name('field-complaints.assign');
        Route::patch('/field-complaints/{fieldComplaint}/status', [FieldComplaintController::class, 'updateStatus'])->name('field-complaints.status');
        Route::post('/field-complaints/{fieldComplaint}/payment', [FieldComplaintController::class, 'recordPayment'])->name('field-complaints.payment');
        Route::delete('/field-complaints/{fieldComplaint}',       [FieldComplaintController::class, 'destroy'])->name('field-complaints.destroy');
        // Milestone routes
        Route::post('/field-complaints/{fieldComplaint}/milestones',           [FieldComplaintController::class, 'milestoneStore'])->name('field-complaints.milestones.store');
        Route::patch('/milestones/{milestone}',                                [FieldComplaintController::class, 'milestoneUpdate'])->name('milestones.update');
        Route::post('/milestones/{milestone}/transfer',                        [FieldComplaintController::class, 'milestoneTransfer'])->name('milestones.transfer');
        Route::post('/milestones/{milestone}/help',                            [FieldComplaintController::class, 'milestoneHelp'])->name('milestones.help');
        Route::delete('/milestones/{milestone}',                               [FieldComplaintController::class, 'milestoneDestroy'])->name('milestones.destroy');

        // Service Types
        Route::get('/service-types',                             [ServiceTypeController::class, 'index'])->name('service-types.index');
        Route::post('/service-types',                            [ServiceTypeController::class, 'store'])->name('service-types.store');
        Route::put('/service-types/{serviceType}',               [ServiceTypeController::class, 'update'])->name('service-types.update');
        Route::delete('/service-types/{serviceType}',            [ServiceTypeController::class, 'destroy'])->name('service-types.destroy');
        Route::patch('/service-types/{serviceType}/toggle',      [ServiceTypeController::class, 'toggle'])->name('service-types.toggle');

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
        Route::post('/jobs/{jobCard}/accept',          [EmployeeDashboardController::class, 'acceptJob'])->name('jobs.accept');

        // Field service jobs for employee
        Route::get('/field-jobs',                                          [EmployeeDashboardController::class, 'fieldJobs'])->name('field-jobs');
        Route::post('/field-jobs/{fieldComplaint}/accept',                 [EmployeeDashboardController::class, 'acceptFieldJob'])->name('field-jobs.accept');
        Route::get('/field-jobs/{fieldComplaint}/complete',                [EmployeeDashboardController::class, 'completeFieldJobForm'])->name('field-jobs.complete');
        Route::post('/field-jobs/{fieldComplaint}/complete',               [EmployeeDashboardController::class, 'completeFieldJob'])->name('field-jobs.complete.save');
    });
});

// Super Admin
Route::prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/login',  [SAAuth::class, 'showLogin'])->name('login');
    Route::post('/login', [SAAuth::class, 'login'])->name('login.post');
    Route::post('/logout',[SAAuth::class, 'logout'])->name('logout');

    Route::middleware(\App\Http\Middleware\SuperAdminAuth::class)->group(function () {
        Route::get('/dashboard', [SADash::class, 'index'])->name('dashboard');
        Route::resource('/shops', SAShop::class)->names('shops');
        Route::post('/shops/{shop}/reset-password', [SAShop::class, 'resetPassword'])->name('shops.reset-password');
        Route::patch('/shops/{shop}/status',        [SAShop::class, 'updateStatus'])->name('shops.update-status');
    });
});

// ── CCTV Module Routes ────────────────────────────────────────────────────────
use App\Http\Controllers\Admin\Cctv\CctvDashboardController;
use App\Http\Controllers\Admin\Cctv\CctvLeadController;
use App\Http\Controllers\Admin\Cctv\CctvSurveyController;
use App\Http\Controllers\Admin\Cctv\CctvQuotationController;
use App\Http\Controllers\Admin\Cctv\CctvProjectController;
use App\Http\Controllers\Admin\Cctv\CctvAssetController;
use App\Http\Controllers\Admin\Cctv\CctvServiceTicketController;
use App\Http\Controllers\Admin\Cctv\CctvAmcController;
use App\Http\Controllers\Admin\Cctv\CctvRepairController;
use App\Http\Controllers\Admin\Cctv\CctvInventoryController;
use App\Http\Controllers\Admin\Cctv\CctvInvoiceController;
use App\Http\Controllers\Admin\Cctv\CctvOrderManagementController;

Route::prefix('admin/cctv')->name('admin.cctv.')->middleware(\App\Http\Middleware\AdminAuth::class)->group(function () {
    Route::get('/dashboard', [CctvDashboardController::class, 'index'])->name('dashboard');

    // Leads
    Route::get('/leads',                 [CctvLeadController::class, 'index'])->name('leads.index');
    Route::get('/leads/create',          [CctvLeadController::class, 'create'])->name('leads.create');
    Route::post('/leads',                [CctvLeadController::class, 'store'])->name('leads.store');
    Route::get('/leads/{lead}',          [CctvLeadController::class, 'show'])->name('leads.show');
    Route::get('/leads/{lead}/edit',     [CctvLeadController::class, 'edit'])->name('leads.edit');
    Route::put('/leads/{lead}',          [CctvLeadController::class, 'update'])->name('leads.update');
    Route::delete('/leads/{lead}',       [CctvLeadController::class, 'destroy'])->name('leads.destroy');

    // Surveys
    Route::get('/surveys',               [CctvSurveyController::class, 'index'])->name('surveys.index');
    Route::get('/surveys/create',        [CctvSurveyController::class, 'create'])->name('surveys.create');
    Route::post('/surveys',              [CctvSurveyController::class, 'store'])->name('surveys.store');
    Route::get('/surveys/{survey}',      [CctvSurveyController::class, 'show'])->name('surveys.show');
    Route::get('/surveys/{survey}/edit', [CctvSurveyController::class, 'edit'])->name('surveys.edit');
    Route::put('/surveys/{survey}',      [CctvSurveyController::class, 'update'])->name('surveys.update');
    Route::delete('/surveys/{survey}',   [CctvSurveyController::class, 'destroy'])->name('surveys.destroy');
    Route::get('/surveys/{survey}/print',[CctvSurveyController::class, 'print'])->name('surveys.print');

    // Quotations
    Route::get('/quotations',                   [CctvQuotationController::class, 'index'])->name('quotations.index');
    Route::get('/quotations/create',            [CctvQuotationController::class, 'create'])->name('quotations.create');
    Route::post('/quotations',                  [CctvQuotationController::class, 'store'])->name('quotations.store');
    Route::get('/quotations/{quotation}',       [CctvQuotationController::class, 'show'])->name('quotations.show');
    Route::get('/quotations/{quotation}/edit',  [CctvQuotationController::class, 'edit'])->name('quotations.edit');
    Route::put('/quotations/{quotation}',       [CctvQuotationController::class, 'update'])->name('quotations.update');
    Route::get('/quotations/{quotation}/pdf',   [CctvQuotationController::class, 'pdf'])->name('quotations.pdf');
    Route::delete('/quotations/{quotation}',    [CctvQuotationController::class, 'destroy'])->name('quotations.destroy');

    // Projects
    Route::get('/projects',                  [CctvProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create',           [CctvProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects',                 [CctvProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}',        [CctvProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/{project}/edit',   [CctvProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}',        [CctvProjectController::class, 'update'])->name('projects.update');
    Route::patch('/projects/{project}/stage', [CctvProjectController::class, 'updateStage'])->name('projects.updateStage');
    Route::patch('/projects/{project}/status',[CctvProjectController::class, 'updateStatus'])->name('projects.updateStatus');
    Route::delete('/projects/{project}',      [CctvProjectController::class, 'destroy'])->name('projects.destroy');

    // Order Management
    Route::get('/order-management', [CctvOrderManagementController::class, 'index'])->name('order-management.index');

    // Invoices
    Route::get('/invoices',                        [CctvInvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create',                 [CctvInvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices',                       [CctvInvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{invoice}',              [CctvInvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/pdf',          [CctvInvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::patch('/invoices/{invoice}/payment',    [CctvInvoiceController::class, 'updatePayment'])->name('invoices.updatePayment');
    Route::delete('/invoices/{invoice}',           [CctvInvoiceController::class, 'destroy'])->name('invoices.destroy');

    // Assets
    Route::get('/assets',                [CctvAssetController::class, 'index'])->name('assets.index');
    Route::get('/assets/create',         [CctvAssetController::class, 'create'])->name('assets.create');
    Route::post('/assets',               [CctvAssetController::class, 'store'])->name('assets.store');
    Route::get('/assets/{asset}',        [CctvAssetController::class, 'show'])->name('assets.show');
    Route::get('/assets/{asset}/edit',   [CctvAssetController::class, 'edit'])->name('assets.edit');
    Route::put('/assets/{asset}',        [CctvAssetController::class, 'update'])->name('assets.update');
    Route::delete('/assets/{asset}',     [CctvAssetController::class, 'destroy'])->name('assets.destroy');

    // Service Tickets
    Route::get('/service-tickets',                          [CctvServiceTicketController::class, 'index'])->name('service-tickets.index');
    Route::get('/service-tickets/create',                   [CctvServiceTicketController::class, 'create'])->name('service-tickets.create');
    Route::post('/service-tickets',                         [CctvServiceTicketController::class, 'store'])->name('service-tickets.store');
    Route::get('/service-tickets/{serviceTicket}',          [CctvServiceTicketController::class, 'show'])->name('service-tickets.show');
    Route::get('/service-tickets/{serviceTicket}/edit',     [CctvServiceTicketController::class, 'edit'])->name('service-tickets.edit');
    Route::put('/service-tickets/{serviceTicket}',          [CctvServiceTicketController::class, 'update'])->name('service-tickets.update');
    Route::patch('/service-tickets/{serviceTicket}/status', [CctvServiceTicketController::class, 'updateStatus'])->name('service-tickets.updateStatus');
    Route::delete('/service-tickets/{serviceTicket}',       [CctvServiceTicketController::class, 'destroy'])->name('service-tickets.destroy');

    // AMC Contracts
    Route::get('/amc',                [CctvAmcController::class, 'index'])->name('amc.index');
    Route::get('/amc/create',         [CctvAmcController::class, 'create'])->name('amc.create');
    Route::post('/amc',               [CctvAmcController::class, 'store'])->name('amc.store');
    Route::get('/amc/{amc}',          [CctvAmcController::class, 'show'])->name('amc.show');
    Route::get('/amc/{amc}/edit',     [CctvAmcController::class, 'edit'])->name('amc.edit');
    Route::put('/amc/{amc}',          [CctvAmcController::class, 'update'])->name('amc.update');
    Route::post('/amc/{amc}/visit',   [CctvAmcController::class, 'addVisit'])->name('amc.addVisit');
    Route::delete('/amc/{amc}',       [CctvAmcController::class, 'destroy'])->name('amc.destroy');

    // Repairs
    Route::get('/repairs',               [CctvRepairController::class, 'index'])->name('repairs.index');
    Route::get('/repairs/create',        [CctvRepairController::class, 'create'])->name('repairs.create');
    Route::post('/repairs',              [CctvRepairController::class, 'store'])->name('repairs.store');
    Route::get('/repairs/{repair}',      [CctvRepairController::class, 'show'])->name('repairs.show');
    Route::get('/repairs/{repair}/edit', [CctvRepairController::class, 'edit'])->name('repairs.edit');
    Route::put('/repairs/{repair}',      [CctvRepairController::class, 'update'])->name('repairs.update');
    Route::delete('/repairs/{repair}',   [CctvRepairController::class, 'destroy'])->name('repairs.destroy');

    // Inventory
    Route::get('/inventory',                          [CctvInventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/create',                   [CctvInventoryController::class, 'create'])->name('inventory.create');
    Route::post('/inventory',                         [CctvInventoryController::class, 'store'])->name('inventory.store');
    Route::get('/inventory/{inventory}',              [CctvInventoryController::class, 'show'])->name('inventory.show');
    Route::get('/inventory/{inventory}/edit',         [CctvInventoryController::class, 'edit'])->name('inventory.edit');
    Route::put('/inventory/{inventory}',              [CctvInventoryController::class, 'update'])->name('inventory.update');
    Route::post('/inventory/{inventory}/stock-in',    [CctvInventoryController::class, 'stockIn'])->name('inventory.stockIn');
    Route::post('/inventory/{inventory}/stock-out',   [CctvInventoryController::class, 'stockOut'])->name('inventory.stockOut');
    Route::delete('/inventory/{inventory}',           [CctvInventoryController::class, 'destroy'])->name('inventory.destroy');
});

// Ajax endpoints
Route::get('/ajax/brands',            [AjaxController::class, 'brands'])->name('ajax.brands');
Route::get('/ajax/faults',            [AjaxController::class, 'faults'])->name('ajax.faults');
Route::get('/ajax/accessories',       [AjaxController::class, 'accessories'])->name('ajax.accessories');
Route::get('/ajax/customer-lookup',   [AjaxController::class, 'customerLookup'])->name('ajax.customer-lookup');
Route::get('/ajax/employees',         [AjaxController::class, 'employeeSearch'])->name('ajax.employees');
Route::post('/ajax/employees',        [AjaxController::class, 'employeeQuickAdd'])->name('ajax.employees.add');

// Mobile PWA app - catch all for SPA routing
Route::get('/mobile', function () {
    return response()->file(public_path('mobile/index.html'));
});
Route::get('/mobile/{any}', function () {
    return response()->file(public_path('mobile/index.html'));
})->where('any', '.*');
