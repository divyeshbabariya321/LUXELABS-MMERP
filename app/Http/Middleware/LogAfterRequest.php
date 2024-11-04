<?php

namespace App\Http\Middleware;

use App\LogRequest;
use App\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class LogAfterRequest
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $logApis = Cache::remember('log_apis', 60, function () {
            return Setting::where('name', '=', 'log_apis')->select('id', 'name', 'val')->first();
        });
        if ($logApis && $logApis->val == 1) {
            $url = $request->fullUrl();
            $ip = $request->ip();

            $startTime = LARAVEL_START;
            $endTime = microtime(true);

            $timeTaken = round($endTime - $startTime, 3);
            $route = app('router')->getRoutes()->match($request);
            $api_name = '';

            if ($route) {
                $api_name = $route->action['controller'];
                $api_name = explode('@', $api_name);
            }

            $response_array = json_decode($response->content(), true);

            try {

                $r = new LogRequest([
                    'ip' => $ip,
                    'url' => $url,
                    'status_code' => $response_array['code'] ?? $response->status(),
                    'method' => $request->method(),
                    'api_name' => isset($api_name[0]) ? $api_name[0] : $api_name,
                    'method_name' => isset($api_name[1]) ? $api_name[1] : $api_name,
                    'request' => json_encode($request->all()),
                    'response' => ! empty($response) ? json_encode($response) : json_encode([]),
                    'message' => isset($response_array['message']) ? $response_array['message'] : '',
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'time_taken' => $timeTaken,
                ]);
                $r->save();
            } catch (Exception $e) {
                Log::info('Log after request has issue '.$e->getMessage());
            }
        }
    }
}
