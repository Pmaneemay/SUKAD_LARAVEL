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
use App\Models\Selection_status;
use App\Models\Student;
use Illuminate\Support\Facades\Crypt;
use Symfony\Contracts\Service\Attribute\Required;
use function Laravel\Prompts\error;

class M_TeamManagementController extends Controller
{
    public function getTeamManagementPage(){
        // Get the user from the session
        $user = auth()->user();
    
        // Retrieve the profile dynamically based on the user's role
        $userProfile = $user->profile;
    
        if ($userProfile) {
            // Now you can directly access desasiswa_id if the profile has it
            $desasiswaId = $userProfile->desasiswa_id;
    
            if ($desasiswaId) {
                // Retrieve the Desasiswa based on the desasiswa_id
                $desasiswa = Desasiswa::where('desasiswa_id', $desasiswaId)->first();
    
                // Pass the desasiswa data to the view
                return view('M_Team_Management', ['desasiswa' => $desasiswa]);
            } else {
                // Handle the case where the desasiswa_id is not found in the profile
                return redirect()->route('errorPage')->withErrors(['message' => 'Desasiswa ID not found.']);
            }
        } 
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

    public function getRegistrationCode(Request $request){
        $manager = Manager::select('registration_code')
            ->where('user_id', $request->manager_id)
            ->first();

        // Check if a manager exists
        if (!$manager) {
            return response()->json([
                'error' => 'Manager not found.'
            ], 404);
        }
        // Decrypt the registration code
        $decrypt_code = decrypt($manager->registration_code);

        return response()->json([
            'code' => $decrypt_code
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

    public function getClubSelection(Request $request)
    {
        $selection = Selection::with([
            'participants.student', // Eager load participants and their associated students
            'participants.status'
        ])
        ->select(
            'selections.*',
            'clubs.club_name',
            'sports.team_size',
            'managers.user_id'
        )
        ->join('clubs', 'clubs.club_id', '=', 'selections.club_id')
        ->join('sports', 'sports.sport_id', '=', 'clubs.sport_id')
        ->join('managers', 'managers.club_id', '=', 'selections.club_id')
        ->where('managers.user_id', session('user_id'))
        ->get();

        $selection_status = Selection_status::all();

        // Add the available slots dynamically
        $selection->each(function ($selection) {
            $playerCount = Student::where('club_id', $selection->club_id)
                ->count(); // Count players directly from the database

            $selection->available = $selection->team_size - $playerCount;
        });

        return response()->json([
            'html' => view('partials.M_clubSelection', [
                'selection' => $selection,
                'selection_status' =>$selection_status
            ])->render()
        ]);

       
    }

    public function create_edit_manager(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'username' => 'required',
            'team' => 'required|exists:clubs,club_id',
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
            $encrpt_code = encrypt($code);
    
            // Save the manager
            $manager = new Manager();
            $manager->user_id = $id; // Corrected assignment
            $manager->name = $request->username;
            $manager->desasiswa_id = $request->desasiswa_id;
            $manager->club_id = $request->team;
            $manager->registration_code = $encrpt_code;
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

    public function create_edit_event(Request $request)
    {
        // Validate the form input
        $validated = $request->validate([
            'is_edit' => 'required|boolean', // Ensure 'is_edit' is either 0 or 1
            'date' => 'required|date',
            'venue' => 'required|string|max:255',
            'time_start' => 'required',
            'time_end' => 'required|after:time_start',
            'registration_deadline' => 'required|date|before_or_equal:date',
            'notes' => 'nullable|string',
        ]);

        // Convert times to HH:mm:ss format
        $timeStart = date("H:i:s", strtotime($request->input('time_start')));
        $timeEnd = date("H:i:s", strtotime($request->input('time_end')));

        // Check if registration deadline has passed
        $isended = now() > $request->input('registration_deadline');

        // Get the 'is_edit' value
        $is_edit = $request->input('is_edit');

        // Handle creation or editing
        if ($is_edit == 0) {
            // Get the club ID from the current manager's session
            $club = Club::select('clubs.club_id')
                ->join('managers', 'managers.club_id', '=', 'clubs.club_id')
                ->where('managers.user_id', session('user_id'))
                ->first(); // Use first() to get a single result

            if ($club) {
                // Create new selection
                $selection = new Selection();
                $selection->selection_date = $request->input('date'); // Match the correct input field name
                $selection->time_start = $timeStart;
                $selection->time_end = $timeEnd;
                $selection->registration_deadline = $request->input('registration_deadline');
                $selection->is_ended = $isended;
                $selection->club_id = $club->club_id;
                $selection->note = $request->input('notes'); // Fixed typo, should be 'notes'
                $selection->venue = $request->input('venue');
                $selection->save();

                return response()->json(['success' => true]);
            } else {
                // Handle the case when the club is not found
                return back()->withErrors(['club_not_found' => 'Club not found for the current user.']);
            }

        }else{
            // Validate the 'selection_id' to ensure it exists
            $validated = $request->validate([
                'selection_id' => 'required|exists:selections,selection_id', // Ensure 'selection_id' exists
            ]);
    
            // Find the existing selection
            $selection = Selection::find($request->input('selection_id'));
    
            if ($selection) {
                // Update the selection details
                $selection->selection_date = $request->input('date');
                $selection->time_start = $timeStart;
                $selection->time_end = $timeEnd;
                $selection->registration_deadline = $request->input('registration_deadline');
                $selection->is_ended = $isended;
                $selection->note = $request->input('notes');
                $selection->venue = $request->input('venue');
                $selection->save();
    
                return response()->json(['success' => true]);
            } else {
                // Handle the case when the selection is not found
                return back()->withErrors(['selection_not_found' => 'Selection not found for the provided ID.']);
            }
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

    public function update_ParticipantStatus(Request $request){

        if($request->selection_id == null){
            return response()->json(['status' => 'error', 'message' => 'Missing selection id']);
        }

        foreach($request->participants as $item){
            if (!in_array($item['status'], [0, 1, 2, 3, 4, 5])) {
                // Handle invalid status
                return response()->json(['status' => 'error', 'message' => 'Invalid status']);
            }
            
            if ($item['priority'] !== null && (!is_numeric($item['priority']) || $item['priority'] < 1)) {
                // Handle invalid priority
                return response()->json(['status' => 'error', 'message' => 'Invalid priority']);
            }
    
            // Perform the update
            $updateStatus = Participant::where([
                                        ['selection_id', '=', $request->selection_id],
                                        ['student_id', '=', $item['student_id']]
                                    ])
                                    ->update([
                                        'selection_status' => $item['status'],
                                        'priority' => $item['priority']
                                    ]);

        }
    
        return response()->json([
            'status' => 'success',
            'message' => 'Updated successfully!',
        ]);
    }

    public function accept_selection(Request $request){
        $userId = session('user_id');

        // Update status to accept
        $accept = Participant::where('student_id', '=', $userId)
                             ->where('selection_id', '=', $request->accept_selection_id)
                             ->update([
                                 'priority' => null,
                                 'selection_status' => 4,  // Accepted status
                             ]);
    
        if($accept === false){
            return response()->json(['status' => 'error', 'message' => 'Error accepting offer!']);
        }
    
        // Register the student to the selected team (club)
        $update_club = Student::where('user_id', '=', $userId)
                              ->update([
                                  'club_id' => $request->accept_team_id
                              ]);
    
        if($update_club === false){
            return response()->json(['status' => 'error', 'message' => 'Error registering club!']);
        }
    
        // Update other registered event to reject status
        Participant::where('student_id', '=', $userId)
                   ->where('selection_id', '!=', $request->accept_selection_id)
                   ->update([
                       'priority' => null,
                       'selection_status' => 5,  // Rejected status
                   ]);
    
        // Update the other participants in the waiting list to Pass
        foreach($request->reject_passed_selections as $item){
            Participant::where('student_id', '!=', $userId)
                       ->where('selection_id', '=', $item)
                       ->where('selection_status', '=', 3)  // Waiting status
                       ->where('priority', '=', 1)  // Priority 1
                       ->update([
                           'priority' => null,
                           'selection_status' => 1,  // Status for Pass
                       ]);
    
            // Move other waiting list participants up the priority list (decrement priority)
            Participant::where('selection_id', '=', $item)
                       ->where('selection_status', '=', 3)  // Waiting status
                       ->decrement('priority', 1);  // Decrement priority
        }
    
        return response()->json(['status' => 'success', 'message' => 'Selection accepted and updated successfully.']);
    }
    

    
}
