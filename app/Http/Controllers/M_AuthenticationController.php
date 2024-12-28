<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Hash;

class M_AuthenticationController extends Controller
{
    public function getLoginPage(){
        return view('M_LoginPage');
    }

    public function login(Request $request)
    {
        // Validate login credentials
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Log the login attempt
        Log::info('Login attempt with email: ' . $request->email);

        // Retrieve the user from the database and eager load the profile
        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {

           
            session(['role' => $user->role, 'profile' => $user->profile]);

            return redirect()->route('HomePage');
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function Logout (){
        Session::forget('role');
        Session::forget('profile');

        return redirect()->route('HomePage');

    }

}
