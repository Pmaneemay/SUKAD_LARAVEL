<?php

namespace App\Http\Controllers;
use App\Models\Desasiswa;
use Illuminate\Support\Facades\Log; 
use Illuminate\Http\Request;
use App\Models\Manager;

class M_TeamManagementController extends Controller
{
    public function getTeamManagementPage(){

        if(!session('role') || session('role') == 'EORG'){
            return redirect()->route('HomePage');
        }

        return view('M_Team_Management');
        
    }

    public function getAllTeamManagers(Request $request){

        
        $desasiswaId = request('desasiswa_id');

        $totalManager = Manager::where('desasiswa_id', $desasiswaId)->count();

        $managers = Manager::select('managers.*', 'clubs.*', 'sports.*', 'users.email')  
        ->join('users','managers.user_id', "=", 'users.user_id')
        ->join('clubs', 'managers.club_id', '=', 'clubs.club_id')  
        ->join('sports', 'clubs.sport_id', '=', 'sports.sport_id') 
        ->where('managers.desasiswa_id', $desasiswaId)  // Apply the where condition
        ->get();
    

        $data = [
            'total_manager' => $totalManager,
             'managers' => $managers,
        ];

        return response()->json([
            'html' => view('partials.M_teamManager', [
                'total_manager' => $totalManager,
                'managers' => $managers
            ])->render()
        ]);

    }
}
