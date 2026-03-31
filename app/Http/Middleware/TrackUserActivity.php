<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\UserDailyActivity;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $now = now();
            
            // Only update last_seen_at if it's been more than 2 minutes to reduce DB load
            if (!$user->last_seen_at || $user->last_seen_at->diffInMinutes($now) >= 2) {
                // Remove timestamps update to avoid updating updated_at constantly
                $user->timestamps = false;
                $user->last_seen_at = $now;
                $user->save();
                
                // Track daily activity
                UserDailyActivity::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'date' => $now->toDateString(),
                    ],
                    [
                        'hits' => \Illuminate\Support\Facades\DB::raw('hits + 1')
                    ]
                );
            }
        }

        return $next($request);
    }
}
