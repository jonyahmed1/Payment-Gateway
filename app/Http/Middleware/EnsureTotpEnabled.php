<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class EnsureTotpEnabled
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user) return $next($request);

        // Example check; require 'totp_secret' on user for TOTP generation/enabled
        if (config('app.require_totp_for_approval') && $user->hasRole('super-admin')) {
            if (empty($user->totp_secret)) {
                return response()->json(['message'=>'2FA is required for this operation.'], 403);
            }
            // You should also check that user has performed a recent 2FA verification token - implementation-specific
        }
        return $next($request);
    }
}