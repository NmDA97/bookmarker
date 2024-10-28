<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function index(){

        $bookmarks = Bookmark::where('user_id', auth()->id())->get();
        return view('home', compact('bookmarks'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'bname' => 'required|string|max:255',
            'burl' => 'required|string',
            'bcategory' => 'nullable|string|max:255',
        ]);

        // dd(auth()->id());

        Bookmark::create([
            'user_id' => auth()->id(),
            'name' => $request->input('bname'),
            'url' => $request->input('burl'),
            'category' => $request->input('bcategory'),
        ]);

        return redirect()->route('home')->with('success', 'Bookmark saved successfully!');
    }
}
