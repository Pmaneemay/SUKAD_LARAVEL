<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class A_ScoreController extends Controller
{
    public function showScoreInput()
    {
        // Fetch matchup data with team details
        $matchups = DB::table('matchups')
            ->join('desasiswas as team1', 'matchups.team1_id', '=', 'team1.desasiswa_id')
            ->join('desasiswas as team2', 'matchups.team2_id', '=', 'team2.desasiswa_id')
            ->join('sports', 'matchups.sport_id', '=', 'sports.sport_id') // Join with sports table
            ->select(
                'matchups.match_id',
                'matchups.group_name',
                'matchups.sport_id',
                'sports.sport_name', // Fetch sport name
                'team1.desasiswa_name as team1_name',
                'team1.logo_path as team1_logo',
                'team2.desasiswa_name as team2_name',
                'team2.logo_path as team2_logo'
            )
            ->get();

        // Group matches by sport
        $groupedMatchups = $matchups->groupBy('sport_name')->map(function ($matches) {
            return $matches->map(function ($match) {
                return [
                    'match_id' => $match->match_id,
                    'group_name' => $match->group_name,
                    'teamA' => [
                        'name' => $match->team1_name,
                        'logo' => asset($match->team1_logo),
                    ],
                    'teamB' => [
                        'name' => $match->team2_name,
                        'logo' => asset($match->team2_logo),
                    ],
                ];
            });
        });

        // Pass the data to the view
        return view('A_ScoreInput', ['groupedMatchups' => $groupedMatchups]);
    }

    public function saveScores(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'match_id' => 'required|string',   // match_id should be a string since it's VARCHAR in your DB
            'team1_score' => 'required|integer',
            'team2_score' => 'required|integer',
        ]);
        
        // Insert the score into the scoring table
        DB::table('scoring')->insert([
            'match_id' => $request->match_id,      // The match_id from the matchups table
            'team1_score' => $request->team1_score,  // Score for team 1
            'team2_score' => $request->team2_score,  // Score for team 2
        ]);
        
        return response()->json(['success' => true]);
    }
    
    
    
}
