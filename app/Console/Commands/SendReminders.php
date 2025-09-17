<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReminderMail;
use App\Models\User;

class SendReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails to users based on their settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::with(['lastRecords', 'intervals', 'remindSetting', 'titles'])->get();

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
                        // メール送信
                        Mail::to($user->email)->send(new ReminderMail($user, null, 14));
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
                                // メール送信
                                Mail::to($user->email)->send(
                                    new ReminderMail($user, null, $interval->interval_days)
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
