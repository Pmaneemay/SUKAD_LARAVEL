<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class M_TeamManagementController extends Controller
{
    public function getTeamManagementPage(){

        if(!session('role') || session('role') == 'EORG'){
            return redirect()->route('HomePage');
        }

        return view('M_Team_Management');
        
    }
}
