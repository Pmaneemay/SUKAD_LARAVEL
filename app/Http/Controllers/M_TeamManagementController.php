<?php

namespace App\Http\Controllers;
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

        $request->validate([
            'desasiswa_id' => 'required|string',
        ]);

        $desasiswaId = $request->input('desasiswa_id');

        $totalManager = Manager::where('desasiswa_id', $desasiswaId)->count();

        $managers = Manager::with(['club.sport'])
        ->where('desasiswa_id', $desasiswaId)
        ->get();

        return response()->json([
            'data' => [
                'total_manager' => $totalManager,
                'managers' => $managers,
            ],
        ]);

    }
}
