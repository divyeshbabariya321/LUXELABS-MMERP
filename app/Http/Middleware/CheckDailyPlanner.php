<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckDailyPlanner
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (Auth::check() && Auth::user()->is_planner_completed == 0 && strpos($request->getPathInfo(), 'dailyplanner') === false) {

            return redirect()->route('dailyplanner.index')->withErrors('Please complete daily planner first!');
        }

        return $next($request);
    }
}
