<?php

namespace App\Services;

use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\Request;

class LoginActivityService
{
    public function __construct(
        private UserActivityService $userActivityService
    ) {
    }

    public function recordLogin(User $user, Request $request): void
    {
        $now = now();
        $sessionId = $request->session()->getId();

        LoginLog::where('id_user', $user->id_user)
            ->where('is_active', true)
            ->update([
                'is_active' => false,
                'logout_at' => $now,
            ]);

        LoginLog::create([
            'id_user' => $user->id_user,
            'session_id' => $sessionId,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'login_at' => $now,
            'last_activity_at' => $now,
            'is_active' => true,
        ]);

        $user->forceFill([
            'current_session_id' => $sessionId,
            'last_login_ip' => $request->ip(),
            'last_login_at' => $now,
            'last_activity_at' => $now,
            'modified_date' => $now,
            'modified_by' => $user->id_user,
        ])->save();

        $this->userActivityService->log(
            $user,
            'Login sistem',
            'User berhasil masuk ke sistem sebagai ' . strtoupper((string) $user->role) . '.',
            $request
        );
    }

    public function touchActivity(User $user, Request $request): void
    {
        $now = now();
        $sessionId = $request->session()->getId();

        $user->forceFill([
            'current_session_id' => $sessionId,
            'last_activity_at' => $now,
        ])->save();

        LoginLog::where('id_user', $user->id_user)
            ->where('session_id', $sessionId)
            ->where('is_active', true)
            ->update([
                'last_activity_at' => $now,
            ]);
    }

    public function recordLogout(?User $user, ?string $sessionId): void
    {
        if (!$user || !$sessionId) {
            return;
        }

        $now = now();

        LoginLog::where('id_user', $user->id_user)
            ->where('session_id', $sessionId)
            ->where('is_active', true)
            ->update([
                'logout_at' => $now,
                'last_activity_at' => $now,
                'is_active' => false,
            ]);

        $user->forceFill([
            'current_session_id' => $user->current_session_id === $sessionId ? null : $user->current_session_id,
            'last_activity_at' => $now,
            'modified_date' => $now,
            'modified_by' => $user->id_user,
        ])->save();
    }
}
