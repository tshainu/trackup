<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TechnicianController;

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
    Route::get('/technician/field-jobs',              [TechnicianController::class, 'fieldJobs']);
    Route::get('/technician/field-jobs/history',      [TechnicianController::class, 'allFieldJobs']);
    Route::get('/technician/field-jobs/{id}',         [TechnicianController::class, 'fieldJobDetail']);
    Route::post('/technician/field-jobs/{id}/accept',        [TechnicianController::class, 'acceptFieldJob']);
    Route::post('/technician/field-jobs/{id}/complete',      [TechnicianController::class, 'completeFieldJob']);
    Route::post('/technician/field-jobs/{id}/extend',        [TechnicianController::class, 'extendFieldJob']);
    Route::post('/technician/field-jobs/{id}/cant-complete', [TechnicianController::class, 'cantCompleteFieldJob']);
});
