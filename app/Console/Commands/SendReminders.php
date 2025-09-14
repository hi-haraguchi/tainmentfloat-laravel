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
        // ユーザーを全取得（必要に応じてアクティブユーザーだけに絞ってもよい）
        $users = User::with(['lastRecords', 'intervals', 'remindSetting'])->get();

        foreach ($users as $user) {
            $mode = $user->remindSetting->mode ?? 0;

            switch ($mode) {
                case 0: // デフォルト（14日）
                    $last = $user->lastRecords->where('kind', null)->first();
                    if ($last && $last->last_recorded_at &&
                        $last->last_recorded_at->lt(now()->subDays(14))) {
                        Mail::to($user->email)->send(new ReminderMail($user, null, 14));
                        $this->info("Default reminder sent to {$user->email}");
                    }
                    break;

                case 1: // カスタム設定
                    foreach ($user->intervals as $interval) {
                        if ($interval->use_custom && $interval->interval_days) {
                            $last = $user->lastRecords->where('kind', $interval->kind)->first();
                            if ($last && $last->last_recorded_at &&
                                $last->last_recorded_at->lt(now()->subDays($interval->interval_days))) {
                                Mail::to($user->email)->send(
                                    new ReminderMail($user, null, $interval->interval_days)
                                );
                                $this->info("Custom reminder sent to {$user->email} (kind={$interval->kind})");
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
