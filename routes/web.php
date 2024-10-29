<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Livewire\Waterlevel as LivewireWaterlevel;
use App\Livewire\Weatherstation as LivewireWeatherstation;
use App\Livewire\Dashboardaws as LivewireDashboardaws;
use App\Livewire\Dashboard as LivewireDashboard;
// Login routes
Route::get('/', [AuthController::class, 'showLoginForm'])
    ->name('login')
    ->middleware('guest');
Route::post('/', [AuthController::class, 'login'])->name('login.submit');

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', LivewireDashboard::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/waterlevel', LivewireWaterlevel::class)->name('waterlevel');
    Route::get('/weatherstation', LivewireWeatherstation::class)->name('weatherstation');
    Route::get('/dashboardaws', LivewireDashboardaws::class)->name('dashboardaws');
});
