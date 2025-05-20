<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ApiKey;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApikeyThrottle
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $plainKey = $request->header('X-API-KEY');
        $hashedKey = hash('sha256', $plainKey);

        $apiKey = ApiKey::where('key', $hashedKey)->where('is_active', true)->first();

        if (!$plainKey || !$apiKey) {
            return response()->json(['message' => 'Invalid API key'], 401);
        }

        if ($apiKey->expires_at && $apiKey->expires_at < now()) {
            return response()->json(['message' => 'API key expired'], 403);
        }

        $limit = $apiKey->rate_limit;
        $window = now()->startOfMinute();

        $cacheKey = "api_key_{$apiKey->id}_{$window->timestamp}";
        $count = cache()->get($cacheKey, 0);

        if ($count >= $limit) {
            return response()->json(['message' => 'Rate limit exceeded'], 429);
        }

        cache()->put($cacheKey, $count + 1, now()->addMinute());
        $apiKey->increment('usage_count');
        $apiKey->update(['last_used_at' => now()]);

        return $next($request);
    }
}
