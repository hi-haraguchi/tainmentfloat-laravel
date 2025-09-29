<?php

use Illuminate\Support\Facades\Route;
use App\Mail\ReminderMail;
use App\Models\User;

Route::get('/', function () {
    return 'Hello, Laravel API!';
});

// メールプレビュー用ルート
Route::get('/preview/reminder/default', function () {
    $user = User::first();
    $lastTitle = $user?->titles()->latest()->first();
    $days = 14;
    $genreName = null; // デフォルトモードなのでジャンル名なし

    return new ReminderMail($user, $lastTitle, $days, $genreName);
});

Route::get('/preview/reminder/custom/{kind}', function ($kind) {
    $user = User::first();
    $lastTitle = $user?->titles()->where('kind', $kind)->latest()->first();
    $days = 7; // 例：7日設定
    $genreMap = [
        0 => '本',
        1 => 'マンガ',
        2 => '映画',
        3 => '音楽',
        4 => 'ポッドキャスト',
        5 => 'TV・動画配信サービス',
        6 => 'その他',
    ];
    $genreName = $genreMap[$kind] ?? null;

    return new ReminderMail($user, $lastTitle, $days, $genreName);
});

require __DIR__.'/auth.php';

