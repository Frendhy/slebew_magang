<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ForemanController;
use App\Http\Controllers\EmergencyReportController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

use App\Http\Controllers\OperatorController;
use App\Http\Controllers\ScheduleController;

Route::middleware(['auth', 'role:admin_manufactur'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/calendar', [AdminController::class, 'calendar'])->name('admin.calendar');
    Route::get('/admin/search', [AdminController::class, 'search'])->name('admin.search');
    Route::get('/admin/spk/{id}', [AdminController::class, 'spkDetail'])->name('admin.spk.detail');

    // Operator Management
    Route::get('/admin/operators', [OperatorController::class, 'index'])->name('admin.operators.index');
    Route::post('/admin/operators', [OperatorController::class, 'store'])->name('admin.operators.store');
    Route::put('/admin/operators/{id}', [OperatorController::class, 'update'])->name('admin.operators.update');
    Route::delete('/admin/operators/{id}', [OperatorController::class, 'destroy'])->name('admin.operators.destroy');

    // Schedule Management
    Route::get('/admin/schedules', [ScheduleController::class, 'index'])->name('admin.schedules.index');
    Route::post('/admin/schedules', [ScheduleController::class, 'store'])->name('admin.schedules.store');
    Route::put('/admin/schedules/{id}', [ScheduleController::class, 'update'])->name('admin.schedules.update');
    Route::delete('/admin/schedules/{id}', [ScheduleController::class, 'destroy'])->name('admin.schedules.destroy');
});

Route::middleware(['auth', 'role:foreman'])->group(function () {
    Route::get('/foreman/dashboard', [ForemanController::class, 'dashboard'])->name('foreman.dashboard');
    Route::get('/foreman/list', [ForemanController::class, 'listSpk'])->name('foreman.list');
    Route::get('/foreman/upcoming',  [ForemanController::class, 'upcoming'])->name('foreman.upcoming');
    Route::get('/foreman/assign',    [ForemanController::class, 'assign'])->name('foreman.assign');
    Route::get('/foreman/spk/{id}/assign', [ForemanController::class, 'spkAssign'])->name('foreman.spk.assign');
    Route::post('/foreman/assign/save',    [ForemanController::class, 'saveAssign'])->name('foreman.assign.save');
    Route::delete('/foreman/assign/{id}',  [ForemanController::class, 'deleteAssign'])->name('foreman.assign.delete');
    Route::get('/foreman/spk/{id}',        [ForemanController::class, 'spkDetail'])->name('foreman.spk.detail');
});

Route::middleware('auth')->group(function () {
    Route::get('/', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // SPK Detail (before starting work)
    Route::get('/production/spk-detail', [\App\Http\Controllers\ProductionController::class, 'showDetail'])->name('production.detail');

    // Production Routes
    Route::get('/production/{step}', [\App\Http\Controllers\ProductionController::class, 'showStep'])->name('production.step');
    Route::get('/production/{step}/complete', [\App\Http\Controllers\ProductionController::class, 'completeStep'])->name('production.complete');

    // Approval Routes
    Route::post('/approval/approve', [\App\Http\Controllers\ApprovalController::class, 'approve'])->name('approval.approve');
    Route::post('/approval/reject', [\App\Http\Controllers\ApprovalController::class, 'reject'])->name('approval.reject');

    // API Routes for AJAX (save batch, extruding log, cleaning log)
    Route::post('/api/batch/save', [\App\Http\Controllers\ProductionController::class, 'saveBatch'])->name('api.batch.save');
    Route::post('/api/extruding/save', [\App\Http\Controllers\ProductionController::class, 'saveExtrudingLog'])->name('api.extruding.save');
    Route::post('/api/cleaning/save', [\App\Http\Controllers\ProductionController::class, 'saveCleaningLog'])->name('api.cleaning.save');
    Route::get('/api/batches', [\App\Http\Controllers\ProductionController::class, 'getBatches'])->name('api.batches');
    Route::get('/api/extruding-logs', [\App\Http\Controllers\ProductionController::class, 'getExtrudingLogs'])->name('api.extruding.logs');
    Route::post('/api/transfer/save', [\App\Http\Controllers\ProductionController::class, 'saveTransfer'])->name('api.transfer.save');
    Route::get('/api/transfer/logs', [\App\Http\Controllers\ProductionController::class, 'getTransferLogs'])->name('api.transfer.logs');

    // Emergency Report Routes (worker submits, all non-worker roles view)
    Route::post('/emergency-report', [EmergencyReportController::class, 'store'])->name('emergency.store');
    Route::get('/emergency-reports', [EmergencyReportController::class, 'index'])->name('emergency.index');
    Route::get('/emergency-reports/{id}', [EmergencyReportController::class, 'show'])->name('emergency.show');
    Route::post('/emergency-reports/{id}/resolve', [EmergencyReportController::class, 'resolve'])->name('emergency.resolve');
});
