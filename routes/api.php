<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AgentController;
use App\Http\Controllers\Api\CustomerController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AgentController::class, 'register']); // Public for agent self-registration
Route::post('/customer/register', [CustomerController::class, 'register']); // Public for customer self-registration

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    // Agent routes
    Route::middleware('is_agent')->group(function () {
        Route::get('/agent/dashboard', [AgentController::class, 'dashboard']);
        Route::get('/agent/profile', [AgentController::class, 'profile']);
        Route::get('/agent/collections', [AgentController::class, 'collections']);
        Route::post('/customer/register/by-agent', [CustomerController::class, 'register']); // Agents register customers
    });
    // Admin routes
    Route::middleware('is_admin')->group(function () {
        Route::get('/agents', [AgentController::class, 'index']);
        Route::get('/agents/{id}', [AgentController::class, 'show']);
        Route::put('/agents/{id}', [AgentController::class, 'update']);
        Route::delete('/agents/{id}', [AgentController::class, 'destroy']);
    });
    // Customer routes
    Route::middleware('is_customer')->group(function () {
        Route::get('/customer/profile', [CustomerController::class, 'profile']);
        Route::post('/customer/profile/update', [CustomerController::class, 'updateProfile']); // New: Profile update
    });
    // Admin or Staff routes for approval
    Route::middleware(['auth:sanctum', 'role:admin,staff'])->group(function () {
        Route::post('/customer/approve/{customer_id}', [CustomerController::class, 'approveProfile']); // New: Approve profile
    });
});