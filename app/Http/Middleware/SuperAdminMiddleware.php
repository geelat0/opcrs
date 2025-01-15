<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role->name === 'SuperAdmin' || Auth::user()->role->name === 'Admin') {
            return $next($request);
        }

        // Redirect to home or any other page
        return redirect('/dash-home')->with('error', 'You do not have superadmin access.');
        return $next($request);
    }
}
