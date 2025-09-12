<?php

namespace App\Http\Controllers;

use App\Models\Thought;
use App\Models\Title;
use App\Models\Tag;
use Illuminate\Http\Request;

class ThoughtController extends Controller
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
     public function storeForTitle(Request $request, Title $title)
    {
        // 他人のタイトルに追加できないように制御
        if ($title->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // バリデーション
        $validated = $request->validate([
            'year'    => 'required|integer',
            'month'   => 'nullable|integer',
            'day'     => 'nullable|integer',
            'part'    => 'nullable|string|max:255',
            'thought' => 'nullable|string',
            'tag'     => 'nullable|string|max:255',
            'link'    => 'nullable|string',
        ]);

        // タグ処理
        $tagId = null;
        if (!empty($validated['tag'])) {
            $tag = Tag::firstOrCreate(['tag' => $validated['tag']]);
            $tagId = $tag->id;
        }

        // Thoughtを作成
        $thought = $title->thoughts()->create([
            'year'    => $validated['year'],
            'month'   => $validated['month'] ?? null,
            'day'     => $validated['day'] ?? null,
            'part'    => $validated['part'] ?? null,
            'thought' => $validated['thought'] ?? null,
            'tag_id'  => $tagId,
            'link'    => $validated['link'] ?? null,
        ]);

        return response()->json([
            'message' => 'Thought added successfully',
            'data'    => $thought->load('tag', 'title'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Thought $thought)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Thought $thought)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
    // Thought + Title を取得して所有者チェック
    $thought = \App\Models\Thought::with('title')->findOrFail($id);

    if ($thought->title->user_id !== auth()->id()) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    // バリデーション
    $validated = $request->validate([
        'year'    => 'required|integer',
        'month'   => 'nullable|integer',
        'day'     => 'nullable|integer',
        'part'    => 'nullable|string|max:255',
        'thought' => 'nullable|string',
        'tag'     => 'nullable|string|max:255',
        'link'    => 'nullable|string',
    ]);

    // タグ処理
    $tagId = null;
    if (!empty($validated['tag'])) {
        $tag = \App\Models\Tag::firstOrCreate(['tag' => $validated['tag']]);
        $tagId = $tag->id;
    }

    // 更新
    $thought->update([
        'year'    => $validated['year'],
        'month'   => $validated['month'] ?? null,
        'day'     => $validated['day'] ?? null,
        'part'    => $validated['part'] ?? null,
        'thought' => $validated['thought'] ?? null,
        'tag_id'  => $tagId,
        'link'    => $validated['link'] ?? null,
    ]);

    return response()->json([
        'message' => 'Thought updated successfully',
        'data'    => $thought->load('tag'),
    ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
    $thought = \App\Models\Thought::with('title')->findOrFail($id);

    // 自分のTitleに紐づいているかチェック
    if ($thought->title->user_id !== auth()->id()) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    $thought->delete();

    return response()->json([
        'message' => 'Thought deleted successfully'
    ]);
    }


    public function editData($id)
    {
    $thought = \App\Models\Thought::with('tag', 'title')
        ->findOrFail($id);

    if ($thought->title->user_id !== auth()->id()) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    return response()->json([
        'id'     => $thought->id,
        'title_id' => $thought->title_id,
        'year'   => $thought->year,
        'month'  => $thought->month,
        'day'    => $thought->day,
        'part'   => $thought->part,
        'thought'=> $thought->thought,
        'link'   => $thought->link,
        'tag'    => $thought->tag?->tag,
    ]);
    }

}
