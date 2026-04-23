<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class CheckIpWhitelist
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user) return $next($request);

        // assume users.ip_whitelist JSON column contains array of allowed CIDRs or IPs
        $list = data_get($user, 'ip_whitelist', []);
        if (empty($list)) return $next($request);

        $ip = $request->ip();
        foreach ($list as $entry) {
            if ($entry === $ip) return $next($request);
            // TODO: for CIDR check, use symfony ip matcher library or custom logic
        }

        return response()->json(['message'=>'Your IP is not allowed to perform this action.'], 403);
    }
}