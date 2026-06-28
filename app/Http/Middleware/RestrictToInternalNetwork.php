<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class RestrictToInternalNetwork
{
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        // Check if the IP falls within standard private local networks (RFC 1918)
        // This natively covers Docker's 172.x, 10.x, and 192.168.x subnets.
        $isPrivateIp = !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);

        if ($isPrivateIp || $ip === '127.0.0.1' || $ip === '::1') {
            return $next($request);
        }

        Log::warning("Unauthorized internal API attempt from IP: {$ip}");
        abort(403, 'Access denied. Endpoint restricted to internal Docker network.');
    }
}