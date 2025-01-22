<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Hash;
use App\Models\Desasiswa;
use App\Models\Manager;
use App\Models\User;
use App\Models\Student;

use function Laravel\Prompts\confirm;

class M_AuthenticationController extends Controller
{
    public function getLoginPage(){
        return view('M_LoginPage');
    }

    public function getSignupPage()
    {
        $desasiswas = Desasiswa::all(); 
        return view('M_SignupPage', compact('desasiswas')); // Pass the data to the view
    }

    public function login(Request $request)
    {
        // Validate login credentials
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            $user = Auth::user();
            session(['user_id' => $user->user_id, 'role' => $user->role, 'profile' => $user->profile]);

            return redirect()->route('HomePage');
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function signup(Request $request){

        //validate required input 
        $request->validate([
            'name' =>'required',
            'email' => 'required|email',
            'password' => 'required',
            'password_confirm' => 'required|same:password',
            'desasiswa' => 'required|exists:desasiswas,desasiswa_id',
            'role' =>'required'
        ]);

        if($request->role == 'student'){
            //make sure student email and matric_no is unique
            $request->validate([
                'email' => 'required|unique:users,email',
                'matrics_no' => 'required|unique:students,matrics_no'
            ]);

            //create user_id
            $timestamp = date('Y-m-d H:i:s');
            $id = 'STUD' . str_replace(['-', ':', ' ', '.'], '', $timestamp);

            //create user and student
            $user = new User([
                'user_id' => $id,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => 'STUD',
            ]);
            $user->save();

            $student = new Student([
                'user_id' => $id,
                'name' => $request->name,
                'matrics_no' => $request->matrics_no,
                'desasiswa_id' => $request->desasiswa,
            ]);

            $student->save();

             // Log in the user after successful signup
            Auth::login($user);
            session(['user_id' => $user->user_id, 'role' => $user->role, 'profile' => $user->profile]);

            // Optionally, redirect the user to their dashboard or a welcome page
            return redirect()->route('HomePage');

        }else {
            $request->validate([
                'email' => 'required|exists:users,email',
                'registration_code' => 'required'
            ]);
        
            // Check for a valid manager account matching the credentials
            $valid_manager = Manager::join('users', 'users.user_id', '=', 'managers.user_id')
                ->where('users.email', $request->email)
                ->where('managers.desasiswa_id', $request->desasiswa)
                ->whereNull('users.password') // Ensure password is null
                ->exists();
        
            if (!$valid_manager) {
                return back()->withErrors(['credentials' => 'Invalid credentials. Please check with your desasiswa admin']);
            }

            $user = User::where('email', $request->email)->first();

            $code = Manager::select('managers.registration_code')
            ->where('user_id', $user->user_id)
            ->first();
        
            if (!$code || $request->registration_code != decrypt($code->registration_code)) {
                return back()->withErrors(['Registration_code' => 'Invalid code. Please check with your desasiswa admin']);
            }
        
            // Update the user's password
            $user->update([
                'password' => bcrypt($request->password),
            ]);
        
            // Log in the user after successful signup
            Auth::login($user);
            session(['user_id' => $user->user_id, 'role' => $user->role, 'profile' => $user->profile]);
        
            // Optionally redirect the user
            return redirect()->route('HomePage')->with('success', 'Signup successful!');
        }
        
    }

    public function Logout (Request $request){
        $request->session()->invalidate();
        Session::flush();
        Auth::logout();
        $request->session()->regenerateToken();
        return redirect()->route('HomePage');

    }

}
