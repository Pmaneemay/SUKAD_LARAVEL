<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
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

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            $user = Auth::user();
            session(['user_id' => $user->user_id, 'role' => $user->role, 'profile' => $user->profile]);

            return redirect()->route('HomePage');
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function Logout (){
        Session::flush();

        return redirect()->route('HomePage');

    }

}
