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
Route::get('/getManagers',[M_TeamManagementController::class, 'getAllTeamManagers'])->name('getManagers');
Route::get('/getClubs',[M_TeamManagementController::class, 'getClubs'])->name('getClubs');
Route::Post('/CreateEditManager',[M_TeamManagementController::class, 'create_edit_manager'])->name('CreateEditManager');
Route::Delete('/deleteManager',[M_TeamManagementController::class, 'delete_manager'])->name('deleteManager');
Route::get('/getSportTeams',[M_TeamManagementController::class, 'getSportTeams'])->name('getSportTeams');
Route::get('/getSelectionEvents',[M_TeamManagementController::class, 'getSelectionEvents'])->name('getSelectionEvents');
Route::Post('/Registerselection',[M_TeamManagementController::class, 'register_selection'])->name('Registerselection');
Route::get('/getRegistered',[M_TeamManagementController::class, 'getRegistered'])->name('getRegistered');
Route::Delete('/deleteRegistration',[M_TeamManagementController::class, 'delete_registration'])->name('deleteRegistration');

// Facility Booking Routes
Route::middleware(['web'])->group(function () {
    Route::get('/bookings', [FacilityBookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings', [FacilityBookingController::class, 'store'])->name('bookings.store');
    Route::get('/past-bookings', [FacilityBookingController::class, 'pastBookings'])->name('bookings.past');
    Route::get('/facility/{facilityId}/availability', [FacilityBookingController::class, 'getAvailability']);
    Route::post('/bookings/{id}/update-status', [FacilityBookingController::class, 'updateStatus'])->name('bookings.update-status');
});

//Matchup Schedule Routes
Route::get('/getMatchups', [C_MatchupScheduleController::class, 'getMatchupPage'])->name('getMatchups');
Route::get('/getDesasiswaLogo', [C_MatchupScheduleController::class, 'getDesasiswaLogo'])->name('getDesasiswaLogo');
Route::post('/startSukad', [C_MatchupScheduleController::class, 'startSukad'])->name('startSukad');
Route::post('/endSukad', [C_MatchupScheduleController::class, 'endSukad'])->name('endSukad');
Route::get('/getMatchupsData', [C_MatchupScheduleController::class, 'getMatchups'])->name('getMatchupsData'); 
Route::get('/getSukadStatus', [C_MatchupScheduleController::class, 'getSukadStatus'])->name('getSukadStatus');

