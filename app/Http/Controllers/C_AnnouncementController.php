<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class C_AnnouncementController extends Controller
{
    public function getAnnouncementPage()
    {
        return view('C_Announcement'); 
    }

    public function saveAnnouncement(Request $request)
    {
        $content = $request->input('content');
        $color = $request->input('color');
        $bold = $request->input('bold') == 'true' ? 1 : 0; 
        $italic = $request->input('italic') == 'true' ? 1 : 0; 
        $underline = $request->input('underline') == 'true' ? 1 : 0; 

        $imagePath = null; 
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(public_path('images'), $imageName);
            $imagePath = 'images/' . $imageName;
        }

        DB::table('announcements')->insert([
            'content' => $content,
            'image_path' => $imagePath,
            'color' => $color,
            'bold' => $bold,
            'italic' => $italic,
            'underline' => $underline,
        ]);

        return response()->json(['message' => 'Announcement saved successfully!']);
    }

    public function getAnnouncements(Request $request)
    {
        $announcements = DB::table('announcements')->get();
        return response()->json($announcements);
    }
}
