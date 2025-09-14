<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RemindSetting;
use App\Models\Interval;

class ReminderController extends Controller
{
    // GET /api/remind-setting
    public function getRemindSetting(Request $request)
    {
        $setting = RemindSetting::where('user_id', $request->user()->id)->first();
        return response()->json(['mode' => $setting->mode]);
    }

    // PUT /api/remind-setting
    public function updateRemindSetting(Request $request)
    {
        $request->validate([
            'mode' => 'required|in:0,1,2',
        ]);

        $setting = RemindSetting::where('user_id', $request->user()->id)->first();
        $setting->update(['mode' => $request->mode]);

        if ($request->mode == 1) {
        // 🔽 カスタムモードON → 全ジャンル use_custom = true
        Interval::where('user_id', $request->user()->id)
            ->update(['use_custom' => true]);
        } else {
        // 🔽 デフォルト or リマインドなし → 全ジャンル use_custom = false
        Interval::where('user_id', $request->user()->id)
            ->update(['use_custom' => false]);
        }

        return response()->json(['success' => true]);
    }

    // GET /api/intervals
    public function getIntervals(Request $request)
    {
        $intervals = Interval::where('user_id', $request->user()->id)->get();
        return response()->json($intervals);
    }

    // PUT /api/intervals
    public function updateIntervals(Request $request)
    {
        $intervals = $request->input('intervals', []);

        foreach ($intervals as $kind => $days) {
            Interval::where('user_id', $request->user()->id)
                ->where('kind', $kind)
                ->update(['interval_days' => $days ?: null, 'use_custom' => true]);
        }

        return response()->json(['success' => true]);
    }
}
