<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\Request;

class LoginManagementController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim((string) $request->query('search', ''));
        $entries = (int) $request->query('entries', 25);
        $entries = in_array($entries, [10, 25, 50, 100], true) ? $entries : 25;

        $now = now();

        $summary = [
            'total_log' => ActivityLog::count(),
            'aktivitas_hari_ini' => ActivityLog::whereDate('activity_time', $now->toDateString())->count(),
            'user_aktif_hari_ini' => ActivityLog::whereDate('activity_time', $now->toDateString())
                ->distinct('id_user')
                ->count('id_user'),
            'sesi_login_hari_ini' => LoginLog::whereDate('login_at', $now->toDateString())->count(),
        ];

        $activityLogs = ActivityLog::query()
            ->with('user')
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($inner) use ($keyword) {
                    $inner->where('activity', 'like', "%{$keyword}%")
                        ->orWhere('detail', 'like', "%{$keyword}%")
                        ->orWhere('ip_address', 'like', "%{$keyword}%")
                        ->orWhere('device', 'like', "%{$keyword}%")
                        ->orWhereHas('user', function ($userQuery) use ($keyword) {
                            $userQuery->where('nama', 'like', "%{$keyword}%")
                                ->orWhere('username', 'like', "%{$keyword}%")
                                ->orWhere('role', 'like', "%{$keyword}%");
                        });
                });
            })
            ->orderByDesc('activity_time')
            ->paginate($entries)
            ->withQueryString();

        return view('pages.admin.login.index', compact(
            'activityLogs',
            'summary',
            'keyword',
            'entries'
        ));
    }
}
