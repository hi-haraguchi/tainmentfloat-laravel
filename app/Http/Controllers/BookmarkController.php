<?php

namespace App\Http\Controllers;


use App\Models\Bookmark;
use App\Models\Thought;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'thought_id' => 'required|exists:thoughts,id',
        ]);

        $userId = $request->user()->id;
        $thoughtId = $request->thought_id;

        // すでにブックマーク済みか確認
        $exists = Bookmark::where('user_id', $userId)
            ->where('thought_id', $thoughtId)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Already bookmarked'], 200);
        }

        $bookmark = Bookmark::create([
            'user_id'   => $userId,
            'thought_id'=> $thoughtId,
        ]);

        return response()->json([
            'message' => 'Bookmarked successfully',
            'data'    => $bookmark,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Bookmark $bookmark)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bookmark $bookmark)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bookmark $bookmark)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Thought $thought)
    {
        $userId = $request->user()->id;

        $bookmark = Bookmark::where('user_id', $userId)
            ->where('thought_id', $thought->id)
            ->first();

        if (! $bookmark) {
            return response()->json(['message' => 'Not bookmarked'], 404);
        }

        $bookmark->delete();

        return response()->json(['message' => 'Bookmark removed']);
    }
}
