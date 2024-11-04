<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class HttpClientTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        $client = Http::defaultClient();

        return $next($request);
    }
}
