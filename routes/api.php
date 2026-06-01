<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TechnicianController;
use App\Http\Controllers\Api\AdminController;

// ── Technician / Employee ─────────────────────────────────────────────────────

// Public
Route::post('/technician/login', [TechnicianController::class, 'login']);

// Protected
Route::middleware(\App\Http\Middleware\TechnicianApiAuth::class)->group(function () {
    Route::post('/technician/logout', [TechnicianController::class, 'logout']);
    Route::get('/technician/me',      [TechnicianController::class, 'me']);

    // Job Cards
    Route::get('/technician/jobs',                    [TechnicianController::class, 'jobs']);
    Route::get('/technician/jobs/all',                [TechnicianController::class, 'allJobs']);
    Route::get('/technician/jobs/{id}',               [TechnicianController::class, 'jobDetail']);
    Route::post('/technician/jobs/{id}/accept',       [TechnicianController::class, 'acceptJob']);
    Route::post('/technician/jobs/{id}/complete',     [TechnicianController::class, 'completeJob']);
    Route::post('/technician/jobs/{id}/assist',       [TechnicianController::class, 'requestAssistance']);

    // Field Complaints
    Route::get('/technician/field-jobs',                     [TechnicianController::class, 'fieldJobs']);
    Route::get('/technician/field-jobs/history',             [TechnicianController::class, 'allFieldJobs']);
    Route::get('/technician/field-jobs/{id}',                [TechnicianController::class, 'fieldJobDetail']);
    Route::post('/technician/field-jobs/{id}/accept',        [TechnicianController::class, 'acceptFieldJob']);
    Route::post('/technician/field-jobs/{id}/complete',      [TechnicianController::class, 'completeFieldJob']);
    Route::post('/technician/field-jobs/{id}/extend',        [TechnicianController::class, 'extendFieldJob']);
    Route::post('/technician/field-jobs/{id}/cant-complete', [TechnicianController::class, 'cantCompleteFieldJob']);
    Route::post('/technician/field-jobs/{id}/update-gps',    [TechnicianController::class, 'updateGps']);
    Route::post('/technician/change-password',             [TechnicianController::class, 'changePassword']);
});

// ── Admin ─────────────────────────────────────────────────────────────────────

// Public
Route::post('/admin/login', [AdminController::class, 'login']);

// Protected
Route::middleware(\App\Http\Middleware\AdminApiAuth::class)->group(function () {
    Route::post('/admin/logout',    [AdminController::class, 'logout']);
    Route::get('/admin/dashboard',  [AdminController::class, 'dashboard']);
    Route::get('/admin/employees',  [AdminController::class, 'employees']);

    // Job Cards
    Route::get('/admin/job-cards',         [AdminController::class, 'jobCards']);
    Route::post('/admin/job-cards',        [AdminController::class, 'createJobCard']);
    Route::get('/admin/job-cards/{id}',    [AdminController::class, 'showJobCard']);
    Route::put('/admin/job-cards/{id}',    [AdminController::class, 'updateJobCard']);
    Route::patch('/admin/job-cards/{id}/status', [AdminController::class, 'updateJobStatus']);

    // Field Complaints
    Route::get('/admin/field-complaints',         [AdminController::class, 'fieldComplaints']);
    Route::post('/admin/field-complaints',        [AdminController::class, 'createFieldComplaint']);
    Route::get('/admin/field-complaints/{id}',    [AdminController::class, 'showFieldComplaint']);
    Route::put('/admin/field-complaints/{id}',    [AdminController::class, 'updateFieldComplaint']);
});
