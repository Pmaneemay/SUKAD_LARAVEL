<?php

use App\Http\Controllers\C_MatchupScheduleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\M_AuthenticationController;
use App\Http\Controllers\M_TeamManagementController;
use App\Http\Controllers\FacilityBookingController;

// Home Page Route
Route::view('/', 'Home')->name('HomePage');

// Authentication Routes
Route::get('/LoginPage', [M_AuthenticationController::class, 'getLoginPage'])->name('LoginPage');
Route::post('/Login', [M_AuthenticationController::class, 'login'])->name('login.submit');
Route::get('/Logout', [M_AuthenticationController::class, 'logout'])->name('logout');

// Team Management Routes
Route::get('/TeamManagementPage', [M_TeamManagementController::class, 'getTeamManagementPage'])->name('TeamManagementPage');
Route::get('/getTeamManagers',[M_TeamManagementController::class, 'getAllTeamManagers'])->name('getManagers');

// Facility Booking Routes
Route::middleware(['web'])->group(function () {
    Route::get('/bookings', [FacilityBookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings', [FacilityBookingController::class, 'store'])->name('bookings.store');
    Route::get('/past-bookings', [FacilityBookingController::class, 'pastBookings'])->name('bookings.past');
    Route::get('/facility/{facilityId}/availability', [FacilityBookingController::class, 'getAvailability']);
    Route::post('/bookings/{id}/update-status', [FacilityBookingController::class, 'updateStatus'])->name('bookings.update-status');
});

//Matchup Schedule Routes
Route::post('/start-sukad', [C_MatchupScheduleController::class, 'startSukad']);
Route::post('/end-sukad', [C_MatchupScheduleController::class, 'endSukad']);
Route::get('/getMatchups', [C_MatchupScheduleController::class, 'getMatchupPage'])->name('getMatchups');
