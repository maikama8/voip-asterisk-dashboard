<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');
    
    Route::get('/dashboard', function () {
        return view('dashboard');
    });

    // Call History
    Route::get('/calls', function () {
        return view('calls.index');
    })->name('calls.index');

    // Agents Management
    Route::get('/agents', function () {
        return view('agents.index');
    })->name('agents.index');

    // Queues Management
    Route::get('/queues', function () {
        return view('queues.index');
    })->name('queues.index');

    // Reports
    Route::get('/reports', function () {
        return view('reports.index');
    })->name('reports.index');

    // Users Management (Admin only)
    Route::get('/users', function () {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
        return view('users.index');
    })->name('users.index');

    // Settings
    Route::get('/settings', function () {
        return view('settings');
    })->name('settings');
});
