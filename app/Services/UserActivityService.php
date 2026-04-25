<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserActivityService
{
    public function log(?User $user, string $activity, ?string $detail = null, ?Request $request = null): void
    {
        ActivityLog::create([
            'id_user' => $user?->id_user,
            'activity_time' => now(),
            'activity' => $activity,
            'detail' => $detail,
            'ip_address' => $request?->ip(),
            'device' => $this->resolveDeviceLabel((string) $request?->userAgent()),
            'user_agent' => (string) $request?->userAgent(),
            'route_name' => $request?->route()?->getName(),
            'http_method' => $request?->method(),
        ]);
    }

    private function resolveDeviceLabel(string $userAgent): string
    {
        if ($userAgent === '') {
            return 'Perangkat tidak diketahui';
        }

        $platform = 'Desktop';
        if (Str::contains($userAgent, ['Android', 'iPhone', 'Mobile'])) {
            $platform = 'Mobile';
        } elseif (Str::contains($userAgent, ['iPad', 'Tablet'])) {
            $platform = 'Tablet';
        }

        $browser = 'Browser';
        if (Str::contains($userAgent, 'Edg/')) {
            $browser = 'Microsoft Edge';
        } elseif (Str::contains($userAgent, 'Chrome/')) {
            $browser = 'Google Chrome';
        } elseif (Str::contains($userAgent, 'Firefox/')) {
            $browser = 'Mozilla Firefox';
        } elseif (Str::contains($userAgent, 'Safari/') && !Str::contains($userAgent, 'Chrome/')) {
            $browser = 'Safari';
        } elseif (Str::contains($userAgent, 'Opera') || Str::contains($userAgent, 'OPR/')) {
            $browser = 'Opera';
        }

        return $platform . ' - ' . $browser;
    }
}
