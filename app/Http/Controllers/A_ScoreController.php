<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class A_ScoreController extends Controller
{
    public function showScoreInput()
    {
        return view('A_ScoreInput'); // Ensure the name matches your Blade file
    }


    public function showMatchups()
    {
        // Fetch match data from the matchups table, including team1_id and team2_id
        $matchups = DB::table('matchups')
            ->select('match_id', 'team1_id', 'team2_id', 'group_name', 'sport_id')
            ->get();
    
        // Pass the matchups to the view
        return view('A_ScoreInput', compact('matchups'));
    }
    
}




 


