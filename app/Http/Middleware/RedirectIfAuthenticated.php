<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        // If no specific guard is provided, check the important guards explicitly
        if (empty($guards)) {
            $guards = ['admin', 'web'];
        }

        foreach ($guards as $guard) {
            // Skip null guards
            if ($guard === null) {
                continue;
            }

            if (Auth::guard($guard)->check()) {
                if ($guard === 'admin') {
                    return redirect()->route('admin.dashboard');
                }
                return redirect()->route('pelanggan.dashboard');
            }
        }

        return $next($request);
    }
}