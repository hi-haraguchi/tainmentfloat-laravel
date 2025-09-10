<?php

namespace App\Http\Controllers;

use App\Models\Title;
use App\Models\Tag;
use Illuminate\Http\Request;

class TitleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
    // ログインユーザーが作成したTitleを取得
    $titles = $request->user()
        ->titles()                     // Userモデルのリレーションを利用
        ->with('thoughts.tag')         // thoughtsとtagをまとめて取得
        ->get();

    return response()->json([
        'message' => 'Your titles retrieved successfully',
        'data'    => $titles,
    ]);
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
        // バリデーション
        $validated = $request->validate([
            'genre'   => 'required|string|max:50',
            'title'   => 'required|string|max:255',
            'author'  => 'required|string|max:255',
            'year'    => 'required|integer',
            'month'   => 'nullable|integer',
            'day'     => 'nullable|integer',
            'part'    => 'nullable|string|max:255',
            'thought' => 'nullable|string',
            'tag'     => 'nullable|string|max:255',
            'link'    => 'nullable|string',
        ]);

        // Tag処理（あれば取得、なければ新規作成）
        $tagId = null;
        if (!empty($validated['tag'])) {
            $tag = Tag::firstOrCreate(['tag' => $validated['tag']]);
            $tagId = $tag->id;
        }

        // Title作成
        $title = Title::create([
            'user_id' => $request->user()->id,
            'genre'   => $validated['genre'],
            'title'   => $validated['title'],
            'author'  => $validated['author'],
            'like'    => false,
        ]);

        // Thought作成
        $title->thoughts()->create([
            'year'    => $validated['year'],
            'month'   => $validated['month'] ?? null,
            'day'     => $validated['day'] ?? null,
            'part'    => $validated['part'] ?? null,
            'thought' => $validated['thought'] ?? null,
            'tag_id'  => $tagId,
            'link'    => $validated['link'] ?? null,
        ]);

        // レスポンス（Titleに紐づくThoughtとTagをまとめて返す）
        return response()->json([
            'message' => 'Title and Thought created successfully',
            'data'    => $title->load('thoughts.tag'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Title $title)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Title $title)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Title $title)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Title $title)
    {
        //
    }
}
