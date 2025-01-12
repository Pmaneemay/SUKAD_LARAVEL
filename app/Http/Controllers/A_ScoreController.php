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
            ->select(
                'matchups.match_id',
                'matchups.group_name',
                'matchups.sport_id',
                'team1.desasiswa_name as team1_name',
                'team1.logo_path as team1_logo',
                'team2.desasiswa_name as team2_name',
                'team2.logo_path as team2_logo'
            )
            ->get();

        // Group matches by sport
        $groupedMatchups = $matchups->groupBy('sport_id')->map(function ($matches) {
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
}
