<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Import the DB facade

class C_MatchupScheduleController extends Controller
{
    public function getMatchupPage()
    {
        $status = DB::table('status_sukads')->first();
        return view('C_MatchupSchedule', ['status' => $status]); 
    }

    public function getSukadStatus(Request $request)
    {
        $status = DB::table('status_sukads')->first();
        return response()->json($status);
    }

    public function startSukad(Request $request)
    {
        // Get all sports from the database
        $sports = collect(DB::table('sports')->get()); 
        $desasiswa = collect(DB::table('desasiswas')->get());

        $desasiswaIds = $desasiswa->pluck('desasiswa_id')->toArray();

        // Generate matchups for each sport
        foreach ($sports as $sport) {
            // Shuffle desasiswa IDs differently for each sport
            shuffle($desasiswaIds);

            // Divide desasiswa into two groups
            $groupA = array_slice($desasiswaIds, 0, count($desasiswaIds) / 2);
            $groupB = array_slice($desasiswaIds, count($desasiswaIds) / 2);

            // Add this debugging output
            echo "Sport ID: " . $sport->sport_id . "<br>"; 
            echo "Group A: " . print_r($groupA, true) . "<br>";
            echo "Group B: " . print_r($groupB, true) . "<br>";

            // Generate matches within each group
            $this->generateMatches((string)$sport->sport_id, 'A', $groupA); // Cast to string
            $this->generateMatches((string)$sport->sport_id, 'B', $groupB); // Cast to string
        }

        // Update status_sukads table
        DB::table('status_sukads')->update(['start' => 1, 'end' => 0]);

        // Return a success response
        return response()->json(['message' => 'SUKAD started successfully!']);
    }

    public function endSukad(Request $request)
    {
        DB::table('matchups')->delete(); // Delete all rows from the matchups table

        // Update status_sukads table
        DB::table('status_sukads')->update(['start' => 0, 'end' => 1]);

        // Return a success response
        return response()->json(['message' => 'SUKAD ended successfully!']); 
    }

    private function generateMatches($sportId, $groupName, $teams)
    {
        // Generate all possible pairings within a group
        $pairs = [];
        for ($i = 0; $i < count($teams) - 1; $i++) {
            for ($j = $i + 1; $j < count($teams); $j++) {
                $pairs[] = [$teams[$i], $teams[$j]];
            }
        }

        // Shuffle the pairings to randomize the match schedule
        shuffle($pairs);

        // Create Matchup records in the database using DB facade
        foreach ($pairs as $pair) {
            DB::table('matchups')->insert([
                'sport_id' => $sportId,
                'group_name' => $groupName,
                'match_id' => uniqid(), // Generate a unique match ID
                'team1_id' => $pair[0],
                'team2_id' => $pair[1],
            ]);
        }
    }

    public function getMatchups(Request $request)
    {
        $sportName = $request->input('sport');

        $sport = DB::table('sports')
                   ->where('sport_name', $sportName)
                   ->first();

        if (!$sport) {
            return response()->json(['error' => 'Sport not found'], 404);
        }

        $sportId = $sport->sport_id;

        $matchups = DB::table('matchups')
                    ->where('sport_id', $sportId)
                    ->get();

        $formattedMatchups = [];
        foreach ($matchups as $matchup) {
            $team1 = DB::table('desasiswas')
                      ->where('desasiswa_id', $matchup->team1_id)
                      ->first();
            $team2 = DB::table('desasiswas')
                      ->where('desasiswa_id', $matchup->team2_id)
                      ->first();

            $formattedMatchups[] = [
                'group_name' => $matchup->group_name,
                'match_id' => $matchup->match_id,
                'team1_id' => $matchup->team1_id, // Include team1_id
                'team2_id' => $matchup->team2_id, // Include team2_id
                'team1_name' => $team1 ? $team1->desasiswa_name : 'Unknown Team',
                'team2_name' => $team2 ? $team2->desasiswa_name : 'Unknown Team',
            ];
        }

        return response()->json($formattedMatchups);
    }

    public function getDesasiswaLogo(Request $request)
    {
        $desasiswaId = $request->input('desasiswa_id');

        $desasiswa = DB::table('desasiswas')
                       ->where('desasiswa_id', $desasiswaId)
                       ->first();

        if ($desasiswa) {
            return response()->json(['logo_path' => $desasiswa->logo_path]);
        } else {
            return response()->json(['error' => 'Desasiswa not found'], 404);
        }
    }
}