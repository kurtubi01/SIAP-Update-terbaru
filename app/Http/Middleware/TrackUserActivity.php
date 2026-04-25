<?php

namespace App\Http\Middleware;

use App\Services\LoginActivityService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    public function __construct(
        private LoginActivityService $loginActivityService
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $lastPing = (int) $request->session()->get('login_activity_last_ping', 0);
            $nowTimestamp = now()->timestamp;

            if (($nowTimestamp - $lastPing) >= 60) {
                $this->loginActivityService->touchActivity($request->user(), $request);
                $request->session()->put('login_activity_last_ping', $nowTimestamp);
            }
        }

        return $next($request);
    }
}
