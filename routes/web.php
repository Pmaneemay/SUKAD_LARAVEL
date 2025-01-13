<?php

use App\Http\Controllers\C_AnnouncementController;
use App\Http\Controllers\C_MatchupScheduleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\M_AuthenticationController;
use App\Http\Controllers\M_TeamManagementController;
use App\Http\Controllers\FacilityBookingController;
use App\Http\Controllers\A_ScoreController;

// Home Page Route
Route::view('/', 'Home')->name('HomePage');

// Authentication Routes
Route::get('/LoginPage', [M_AuthenticationController::class, 'getLoginPage'])->name('LoginPage');
Route::post('/Login', [M_AuthenticationController::class, 'login'])->name('login.submit');
Route::get('/Logout', [M_AuthenticationController::class, 'logout'])->name('logout');

// Team Management Routes
Route::get('/TeamManagementPage', [M_TeamManagementController::class, 'getTeamManagementPage'])->name('TeamManagementPage');
Route::get('/getManagers',[M_TeamManagementController::class, 'getAllTeamManagers'])->name('getManagers');

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

//Announcement Routes
Route::get('/Announcement', [C_AnnouncementController::class, 'getAnnouncementPage'])->name('Announcement');
Route::post('/saveAnnouncement', [C_AnnouncementController::class, 'saveAnnouncement'])->name('saveAnnouncement');
Route::get('/getAnnouncements', [C_AnnouncementController::class, 'getAnnouncements'])->name('getAnnouncements');

// Score Input Routes
//Route::get('/score-input', [A_ScoreController::class, 'showScoreInput'])->name('score.input');
//Route::post('/submit-score', [A_ScoreController::class, 'submitScore'])->name('score.submit');

// Route to display the score input page
Route::get('/score-input', [A_ScoreController::class, 'showScoreInput'])->name('score.input');
Route::post('/save-scores', [A_ScoreController::class, 'saveScores']);
Route::get('/get-scores/{sport}', [ScoreController::class, 'getScores']);

