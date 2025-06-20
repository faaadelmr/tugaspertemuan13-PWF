<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/api/chart-data', [DashboardController::class, 'getChartData'])->name('chart.data');
Route::get('/api/detailed-stats', [DashboardController::class, 'getDetailedStats'])->name('detailed.stats');
