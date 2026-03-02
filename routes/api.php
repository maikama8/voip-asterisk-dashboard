<?php

use App\Http\Controllers\Api\CallController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/dashboard/active-calls', [DashboardController::class, 'activeCalls']);
    Route::get('/dashboard/agents', [DashboardController::class, 'agents']);
    Route::get('/dashboard/queues', [DashboardController::class, 'queues']);

    Route::post('/calls/hangup', [CallController::class, 'hangup']);
    Route::post('/calls/transfer', [CallController::class, 'transfer']);

    Route::get('/reports/call-history', [ReportController::class, 'callHistory']);
    Route::get('/reports/daily-stats', [ReportController::class, 'dailyStats']);

    // Settings API
    Route::post('/settings/test-connection', [SettingsController::class, 'testConnection']);
    Route::post('/settings/asterisk', [SettingsController::class, 'updateAsteriskSettings']);
    Route::get('/settings/sip-peers', [SettingsController::class, 'getSipPeers']);
});
