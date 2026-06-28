<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckGroupPermission
{
    public function handle(Request $request, Closure $next, $permission): Response
    {
        // Check if user is logged in AND has the specific boolean set to true
        if (!auth()->check() || !auth()->user()->hasPermission($permission)) {
            // Throw a 403 Forbidden error page
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}