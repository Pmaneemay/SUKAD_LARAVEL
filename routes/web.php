<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\M_AuthenticationController;
use App\Http\Controllers\M_TeamManagementController;

Route::view('/','Home')->name('HomePage');

//Authentication routes
Route::get('/LoginPage', [M_AuthenticationController::class, 'getLoginPage'])->name('LoginPage');
Route::post('/Login', [M_AuthenticationController::class, 'login'])->name('login.submit');
Route::get('/Logout', [M_AuthenticationController::class, 'logout'])->name('logout');

//Team management routes
Route::get('/TeamManagementPage',[M_TeamManagementController::class, 'getTeamManagementPage'])->name('TeamManagementPage');