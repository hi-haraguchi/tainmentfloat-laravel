<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{

    public function indexShared(Request $request)
{
    $query = $request->input('q'); // 検索ワード（任意）
    $user = $request->user();      // ログインユーザー

    $tags = \App\Models\Tag::when($query, function ($q) use ($query) {
            $q->where('tag', 'like', "%{$query}%");
        })
        ->with(['thoughts' => function ($q) {
            $q->whereHas('title', function ($q2) {
                $q2->where('genre', '!=', 'その他'); // 「その他」を除外
            })->with(['title' => function ($q3) {
                $q3->select('id', 'genre', 'kind','title', 'author');
            }]);
        }])
        ->get()
        ->map(function ($tag) use ($user) {
            return [
                'id'   => $tag->id,
                'tag'  => $tag->tag,
                'records' => $tag->thoughts->map(function ($thought) use ($user) {
                    return [
                        'thought_id' => $thought->id,
                        'genre'      => $thought->title->genre,
                        'kind'       => $thought->title->kind, 
                        'title'      => $thought->title->title,
                        'author'     => $thought->title->author,
                        'part'       => $thought->part,
                        // すでにブックマークしているかチェック
                        'bookmarked' => $user 
                            ? $user->bookmarks()->where('thought_id', $thought->id)->exists()
                            : false,
                    ];
                }),
            ];
        });

    return response()->json($tags);
}

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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        //
    }
}
