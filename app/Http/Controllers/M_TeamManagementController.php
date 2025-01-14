<?php

namespace App\Http\Controllers;
use App\Models\Desasiswa;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Manager;
use App\Models\Club;
use App\Models\User;
use App\Models\selection;
use App\Models\Participant;
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

    public function getSportTeams(Request $request)
    {
        $role = session('role');
        $userId = session('user_id');
    
        $clubs = Club::with(['players.credentials' => function ($query) {
            $query->select('user_id', 'email'); // Only fetch user_id and email
        }])
            ->select(
                'clubs.*', 
                'managers.name as manager_name', 
                'sports.sport_name', 
                'sports.team_size'
            )
            ->leftJoin('managers', 'managers.club_id', '=', 'clubs.club_id')
            ->join('sports', 'sports.sport_id', '=', 'clubs.sport_id')
            ->when($role === 'TMNG', function ($query) use ($userId) {
                return $query->where('managers.user_id', $userId);
            })
            ->when($role === 'STUD', function ($query) use ($userId) {
                return $query->whereHas('players', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                });
            })
            ->where('clubs.desasiswa_id', $request->desasiswa_id)
            ->get();
    
        
        $clubs->each(function ($club) {
            $club->total_players = $club->players->count();
        });

        log::info('clubs'.$clubs);
    
        return response()->json([
            'html' => view('partials.M_sportTeam', compact('clubs'))->render()
        ]);
    }

    public function getSelectionEvents(Request $request){
        
        $selections = Selection::with('club.players') 
                    ->select('selections.*', 'clubs.club_name', 'sports.team_size')
                    ->join('clubs', 'clubs.club_id', '=', 'selections.club_id')
                    ->join('sports', 'sports.sport_id', '=', 'clubs.sport_id')
                    ->where('clubs.desasiswa_id', $request->desasiswa_id)
                    ->get();
    
        $selections->each(function($selection){
            $userId = session('user_id');
            $selection->available = $selection->team_size - $selection->club->players->count() ;

           $selection->is_registered = DB::table('participants')
            ->where('student_id', $userId)
            ->where('selection_id', $selection->selection_id)
            ->exists();
        });

        log::info($selections);

        return response()->json([
            'html' => view('partials.M_selectionEvent', compact('selections'))->render()
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
                'message' => 'New Manager added!',
            ]);
        }else {

            Manager::where('user_id', $request->user_id)->update([
                'name' => $request->username,
            ]);
            
            User::where('user_id', $request->user_id)->update([
                'email' => $request->email,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Manager detail updated!',
            ]);
        }
    
    }

    public function delete_manager(Request $request){
        $manager = Manager::where('user_id',$request->user_id)->delete();
        $user = User::where('user_id',$request->user_id)->delete();

        if($manager == 1 && $user == 1){

            return response()->json([
                'status' => 'success',
                'message' => 'Manager deleted!',
            ]);
        }else{

            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting manager!'
            ], 400);
        }
    }

    public function register_selection(Request $request){
        $participant = new Participant();
        $participant->selection_id = $request->selection_id;
        $participant->student_id = session('user_id');
        $participant->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Registered',
        ]);
    }

    public function getRegistered(Request $request){
        $registered = Selection::with('club.players') 
                    ->select('selections.*','clubs.club_name', 'sports.team_size', 'selection_status.id as status_id', 'selection_status.type as status_type', 'selection_status.description as status_text')
                    ->join('clubs', 'clubs.club_id', '=', 'selections.club_id')
                    ->join('sports', 'sports.sport_id', '=', 'clubs.sport_id')
                    ->join('participants', 'participants.selection_id','=','selections.selection_id')
                    ->join('selection_status', 'selection_status.id','=','participants.selection_status')
                    ->where('participants.student_id',session('user_id'))
                    ->get();

            $registered->each(function($registered){
                $registered->available = $registered->team_size - $registered->club->players->count() ;
            });

            return response()->json([
                'html' => view('partials.M_studentEvent', [
                    'registered' => $registered,
                ])->render()
            ]);
    }

    public function delete_registration(Request $request){
        $registration = Participant::where([
                        ['selection_id', '=', $request->selection_id],
                        ['student_id', '=', session('user_id')],
                    ])->delete();

        if($registration){
            return response()->json([
                'status' => 'success',
                'message' => 'Registration removed!',
            ]);
        }else{
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting registration!'
            ], 400);
        }
    }

    
}
