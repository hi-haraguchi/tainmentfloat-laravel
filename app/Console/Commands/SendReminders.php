<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReminderMail;
use App\Models\User;

class SendReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send reminder emails to users based on their settings';

    public function handle()
    {
        $users = User::with(['lastRecords', 'intervals', 'remindSetting', 'titles'])->get();

        // kind → ジャンル名マップ
        $genreMap = [
            0 => '本',
            1 => 'マンガ',
            2 => '映画',
            3 => '音楽',
            4 => 'ポッドキャスト',
            5 => 'TV・動画配信サービス',
            6 => 'その他',
        ];

        foreach ($users as $user) {
            $mode = $user->remindSetting->mode ?? 0;

            switch ($mode) {
                case 0: // デフォルト（14日ごと）
                    $last = $user->lastRecords->where('kind', null)->first();

                    if (
                        $last && $last->last_recorded_at &&
                        $last->last_recorded_at->lt(now()->subDays(14)) &&
                        (
                            !$last->last_reminded_at ||
                            $last->last_reminded_at->lt(now()->subDays(14))
                        )
                    ) {
                        // 最新タイトル（全体）
                        $lastTitle = $user->titles()->latest()->first();

                        // メール送信（ジャンル名は null）
                        Mail::to($user->email)->send(
                            new ReminderMail($user, $lastTitle, 14, null)
                        );
                        $this->info("Default reminder sent to {$user->email}");

                        // ★ 全体(null)を更新
                        $last->update(['last_reminded_at' => now()]);

                        // ★ 全ジャンルも更新
                        foreach ($user->lastRecords as $record) {
                            if (!is_null($record->kind)) {
                                $record->update(['last_reminded_at' => now()]);
                            }
                        }
                    }
                    break;

                case 1: // カスタム設定
                    foreach ($user->intervals as $interval) {
                        if ($interval->use_custom && $interval->interval_days) {
                            $last = $user->lastRecords->where('kind', $interval->kind)->first();

                            if (
                                $last && $last->last_recorded_at &&
                                $last->last_recorded_at->lt(now()->subDays($interval->interval_days)) &&
                                (
                                    !$last->last_reminded_at ||
                                    $last->last_reminded_at->lt(now()->subDays($interval->interval_days))
                                )
                            ) {
                                // 最新タイトル（ジャンル別）
                                $lastTitle = $user->titles()->where('kind', $interval->kind)->latest()->first();
                                $genreName = $genreMap[$interval->kind] ?? null;

                                // メール送信
                                Mail::to($user->email)->send(
                                    new ReminderMail($user, $lastTitle, $interval->interval_days, $genreName)
                                );
                                $this->info("Custom reminder sent to {$user->email} (kind={$interval->kind})");

                                // ★ ジャンル用を更新
                                $last->update(['last_reminded_at' => now()]);

                                // ★ 全体(null)も更新
                                $overall = $user->lastRecords->where('kind', null)->first();
                                if ($overall) {
                                    $overall->update(['last_reminded_at' => now()]);
                                }
                            }
                        }
                    }
                    break;

                case 2: // リマインドなし
                    $this->info("Reminders disabled for {$user->email}");
                    break;
            }
        }

        $this->info('Reminder check completed!');
    }
}
