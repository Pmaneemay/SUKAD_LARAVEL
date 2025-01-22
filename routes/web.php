<?php

use App\Http\Controllers\C_AnnouncementController;
use App\Http\Controllers\C_MatchupScheduleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\M_AuthenticationController;
use App\Http\Controllers\M_TeamManagementController;
use App\Http\Controllers\FacilityBookingController;
use App\Http\Controllers\A_ScoreController;
use App\Http\Middleware\RoleMiddleware;

// Home Page Route
Route::view('/', 'Home')->name('HomePage');

// Authentication Routes
Route::get('/login', [M_AuthenticationController::class, 'getLoginPage'])->name('login');
Route::get('/signup', [M_AuthenticationController::class, 'getSignupPage'])->name('signup');
Route::post('/login_submit', [M_AuthenticationController::class, 'login'])->name('login.submit');
Route::post('/signup_submit', [M_AuthenticationController::class, 'signup'])->name('signup.submit');
Route::get('/logout', [M_AuthenticationController::class, 'logout'])->name('logout');

// Team Management Routes  
Route::middleware(['auth',roleMiddleware::class.':DSAD,TMNG,STUD'])->group(function (){
    Route::get('/TeamManagementPage', [M_TeamManagementController::class, 'getTeamManagementPage'])->name('TeamManagementPage');
    Route::get('/getManagers',[M_TeamManagementController::class, 'getAllTeamManagers'])->name('getManagers');
    Route::get('/getRegistrationCode',[M_TeamManagementController::class, 'getRegistrationCode'])->name('getRegistrationCode');
    Route::get('/getClubs',[M_TeamManagementController::class, 'getClubs'])->name('getClubs');
    Route::Post('/CreateEditManager',[M_TeamManagementController::class, 'create_edit_manager'])->name('CreateEditManager');
    Route::Delete('/deleteManager',[M_TeamManagementController::class, 'delete_manager'])->name('deleteManager');
    Route::get('/getSportTeams',[M_TeamManagementController::class, 'getSportTeams'])->name('getSportTeams');
    Route::get('/getSelectionEvents',[M_TeamManagementController::class, 'getSelectionEvents'])->name('getSelectionEvents');
    Route::Post('/Registerselection',[M_TeamManagementController::class, 'register_selection'])->name('Registerselection');
    Route::get('/getRegistered',[M_TeamManagementController::class, 'getRegistered'])->name('getRegistered');
    Route::Delete('/deleteRegistration',[M_TeamManagementController::class, 'delete_registration'])->name('deleteRegistration');
    Route::get('/getClubSelection',[M_TeamManagementController::class, 'getClubSelection'])->name('getClubSelection');
    Route::Post('/updateParticipantStatus',[M_TeamManagementController::class, 'update_ParticipantStatus'])->name('updateParticipantStatus');
    Route::Post('/Acceptselection',[M_TeamManagementController::class, 'accept_selection'])->name('Acceptselection');
    Route::Post('/Event_Submit',[M_TeamManagementController::class, 'create_edit_event'])->name('Event.submit');
});

// Facility Booking Routes
Route::middleware(['auth'])->group(function () {
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

// Route to display the score input page
Route::get('/score-input', [A_ScoreController::class, 'showScoreInput'])->name('score.input');
Route::post('/save-scores', [A_ScoreController::class, 'saveScores']);
Route::get('/score-view', [A_ScoreController::class, 'showViewScore'])->name('score.view');

