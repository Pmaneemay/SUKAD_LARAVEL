<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matchup;
use App\Models\Sport;
use App\Models\Desasiswa;

class C_MatchupScheduleController extends Controller
{
    public function getMatchupPage()
    {
        return view('C_MatchupSchedule');
    }

    public function startSukad(Request $request)
    {
        // Clear existing matchups for a fresh start
        Matchup::truncate(); 

        $sports = ['FOOTBALL', 'NETBALL', 'BASKETBALL', 'TENNIS'];
        $dorms = Desasiswa::pluck('desasiswa_id')->toArray();

        foreach ($sports as $sport) {
            $shuffledDorms = $this->shuffleArray($dorms);
            $groupA = array_slice($shuffledDorms, 0, 4);
            $groupB = array_slice($shuffledDorms, 4, 8);

            $this->generateMatchups($groupA, $sport, 'groupA');
            $this->generateMatchups($groupB, $sport, 'groupB');
        }

        return response()->json(['message' => 'SUKAD has started! Schedules generated.']);
    }

    public function endSukad()
    {
        Matchup::truncate(); // Clear all matchups from the database
        return response()->json(['message' => 'SUKAD has ended. Schedules cleared.']);
    }

    private function shuffleArray(array $array)
    {
        shuffle($array);
        return $array;
    }

    private function generateMatchups(array $group, string $sportName, string $groupName)
    {
        $sport = Sport::where('sport_name', $sportName)->firstOrFail(); 

        foreach ($group as $i => $team1Id) {
            for ($j = $i + 1; $j < count($group); $j++) {
                $team2Id = $group[$j];

                Matchup::create([
                    'sport_id' => $sport->sport_id,
                    'group_name' => $groupName,
                    'match_id' => strtolower($sport->sport_name) . '-' . uniqid(), 
                    'team1_id' => $team1Id,
                    'team2_id' => $team2Id,
                ]);
            }
        }
    }

    public function getMatchups(Request $request, $sportName)
    {
        $sport = Sport::where('sport_name', $sportName)->firstOrFail();

        $groupA = Matchup::where('sport_id', $sport->sport_id)
            ->where('group_name', 'groupA')
            ->with('team1', 'team2') 
            ->get();

        $groupB = Matchup::where('sport_id', $sport->sport_id)
            ->where('group_name', 'groupB')
            ->with('team1', 'team2')
            ->get();

        $matchups = [];
        $i = $j = 0;

        // Interleave matches from Group A and Group B
        while ($i < $groupA->count() || $j < $groupB->count()) {
            if ($i < $groupA->count()) {
                $matchups[] = $groupA[$i];
                $i++;
            }
            if ($j < $groupB->count()) {
                $matchups[] = $groupB[$j];
                $j++;
            }
        }

        return response()->json($matchups);
    }
}