<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Closure;

class SendgridEventMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->isKeyValid($request)) {
            return response()->json(['message' => 'Unauthorized request!'], 401);
        }

        return $next($request);
    }

    private function isKeyValid(Request $request): bool
    {
        if (! config('sendgridevents.url_secret_key')) {
            return true;
        }
        if ($request->input('key') == config('sendgridevents.url_secret_key')) {
            return true;
        }

        return false;
    }
}
