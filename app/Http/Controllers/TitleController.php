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

        // 日本語 → 数字 のマップ
        $map = [
            '本' => 0,
            'マンガ' => 1,
            '映画' => 2,
            '音楽' => 3,
            'ポッドキャスト' => 4,
            'TV・動画配信サービス' => 5,
        ];

        // kind を算出
        $kind = $map[$validated['genre']] ?? null;

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


        // LastRecord 更新（全体用）
        \App\Models\LastRecord::updateOrCreate(
        ['user_id' => $request->user()->id, 'kind' => null], 
        ['last_recorded_at' => now()]
        );

        // LastRecord 更新（ジャンル用）
        if (!is_null($kind)) {
            \App\Models\LastRecord::updateOrCreate(
                ['user_id' => $request->user()->id, 'kind' => $kind],
                ['last_recorded_at' => now()]
            );
        }

        // レスポンス（Titleに紐づくThoughtとTagをまとめて返す）
        return response()->json([
            'message' => 'Title and Thought created successfully',
            'data'    => $title->load('thoughts.tag'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
    // ログインユーザーのtitleのみ取得
    $title = \App\Models\Title::where('user_id', auth()->id())
        ->with('thoughts.tag') // thoughts と tag をまとめて取得
        ->findOrFail($id);

    return response()->json($title);
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
    public function update(Request $request, $id)
    {
    // ログインユーザーのTitleのみ取得
    $title = \App\Models\Title::where('user_id', auth()->id())
        ->findOrFail($id);

    // バリデーション
    $validated = $request->validate([
        'genre'  => 'required|string|max:50',
        'title'  => 'required|string|max:255',
        'author' => 'required|string|max:255',
        'like'   => 'boolean',
    ]);

    // 更新
    $title->update($validated);

        return response()->json([
            'message' => 'Title updated successfully',
            'data'    => $title,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $title = \App\Models\Title::where('user_id', auth()->id())->findOrFail($id);

        $title->delete(); // cascadeOnDelete で thoughts も削除される

        return response()->json([
            'message' => 'Title and related thoughts deleted successfully'
        ]);
    }


    public function editData($id)
    {
    $title = \App\Models\Title::where('user_id', auth()->id())
        ->select('id', 'genre', 'title', 'author', 'like') // 必要なカラムだけ
        ->findOrFail($id);

    return response()->json($title);
    }

    public function search(Request $request)
    {
    $query = $request->input('q');

    $titles = \App\Models\Title::where('user_id', auth()->id()) // 自分の記録だけ
        ->where(function ($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
                ->orWhere('author', 'like', "%{$query}%")
                ->orWhereHas('thoughts', function ($q2) use ($query) {
                    $q2->where('part', 'like', "%{$query}%")
                        ->orWhere('thought', 'like', "%{$query}%")
                        ->orWhereHas('tag', function ($q3) use ($query) {
                            $q3->where('tag', 'like', "%{$query}%");
                    });
            });
        })
        ->with('thoughts.tag') // thoughts と tag を同時に取得
        ->get();

    return response()->json([
        'message' => 'Search results',
        'data'    => $titles,
    ]);
    }

    public function timeline(Request $request)
{
    $titles = \App\Models\Title::where('user_id', auth()->id())
        ->with(['thoughts.tag'])
        ->get();

    // thoughtsをyear, monthごとにまとめる
    $timeline = [];
    foreach ($titles as $title) {
        foreach ($title->thoughts as $thought) {
            $year = $thought->year;
            $month = $thought->month ?? '00'; // 月が無い場合は "00" とする

            $timeline[$year][$month][] = [
                'id'     => $title->id,
                'genre'  => $title->genre,
                'title'  => $title->title,
                'author' => $title->author,
                'thought'=> $thought->thought,
                'tag'    => $thought->tag?->tag,
                'day'    => $thought->day,
            ];
        }
    }

 krsort($timeline); // 年は降順（新しい年から）

foreach ($timeline as &$months) {
    uksort($months, function($a, $b) {
        if ($a === '00') return 1;   // 月なしを最後に
        if ($b === '00') return -1;
        return intval($b) <=> intval($a); // 月は数値で降順（12 → 1）
    });
}

    return response()->json($timeline);
}



}
