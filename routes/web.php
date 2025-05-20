<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Middleware\Authenticate;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Auth;

// Root route: Redirect to login or dashboard based on authentication
Route::get('/', function () {
    if (Auth::guard('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('admin.login');
});

// Authentication routes
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('submit.login');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
Route::get('/admin/reset-password', [AdminAuthController::class, 'showResetForm'])->name('admin.password.request');

// Protected routes
Route::middleware([Authenticate::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('customers', CustomerController::class)->names('admin.customers');
    Route::resource('agents', AgentController::class)->names('admin.agents');
    Route::resource('permissions', PermissionController::class)->names('admin.permissions');
    Route::post('/admin/customers/{id}/update-status', [App\Http\Controllers\CustomerController::class, 'updateProfileStatus'])->name('admin.customers.update-status')->middleware('auth:admin');
});