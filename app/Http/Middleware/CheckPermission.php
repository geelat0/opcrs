<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     * @param  string  $permissions
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permissions): Response
    {

        if (Auth::check() && Auth::user()->role->name === 'SuperAdmin' || Auth::user()->role->name === 'Admin') {
            return $next($request);
        }

        if (!Auth::user()->role->hasPermission($permissions)) {
            // If not, return a 403 Forbidden response
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
