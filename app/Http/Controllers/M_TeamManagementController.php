<?php

namespace App\Http\Controllers;
use App\Models\Desasiswa;
use Illuminate\Support\Facades\Log; 
use Illuminate\Http\Request;
use App\Models\Manager;
use App\Models\Club;
use App\Models\User;
use Symfony\Contracts\Service\Attribute\Required;

use function Laravel\Prompts\error;

class M_TeamManagementController extends Controller
{
    public function getTeamManagementPage(){

        if(!session('role') || session('role') == 'EORG'){
            return redirect()->route('HomePage');
        }

        return view('M_Team_Management');
        
    }

    public function getAllTeamManagers(Request $request){

        
        $desasiswaId = $request->desasiswa_id;

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

    public function getClubs(Request $request)
    {
        $clubs = Club::select('clubs.*')
                ->where('desasiswa_id', $request->desasiswa_id)->get();

        return response()->json([
            'clubs' => $clubs
        ]); 
    }

    public function create_edit_manager(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'username' => 'required',
            'team' => 'required',
        ]);

        $is_edit = filter_var($request->is_edit, FILTER_VALIDATE_BOOLEAN);
    
        if ($is_edit == false) {
            // Check if a manager already exists for the team
            $managerExists = Manager::where('club_id', $request->team)->exists();
            if ($managerExists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Manager already registered for this team.'
                ], 400);
            }
    
            // Check if the email is already registered
            $emailExists = User::where('email', $request->email)->exists();
            if ($emailExists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Email is already registered for another manager.'
                ], 400);
            }
    
            // Generate unique IDs
            $timestamp = date('Y-m-d H:i:s');
            $id = 'TMNG' . str_replace(['-', ':', ' ', '.'], '', $timestamp);
            $code = 'CD' . str_replace(['-', ':', ' ', '.'], '', $timestamp);
    
            // Save the manager
            $manager = new Manager();
            $manager->user_id = $id; // Corrected assignment
            $manager->name = $request->username;
            $manager->desasiswa_id = $request->desasiswa_id;
            $manager->club_id = $request->team;
            $manager->registration_code = $code;
            $manager->save();
    
            // Save the user
            $user = new User();
            $user->user_id = $id;
            $user->email = $request->email;
            $user->role = 'TMNG';
            $user->save();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Manager successfully added.',
            ]);
        }
    
        // Logic for editing an existing manager can go here
        return response()->json([
            'status' => 'error',
            'message' => 'Edit functionality is not implemented yet.',
        ], 400);
    }
    
}
