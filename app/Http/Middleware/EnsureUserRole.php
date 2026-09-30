<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
{
    if (!Auth::check()) {
        return redirect('/login');
    }

    $userRole = strtolower(trim(Auth::user()->role));

    $allowedRoles = array_map(function ($role) {
        return strtolower(trim($role));
    }, $roles);

    if (!in_array($userRole, $allowedRoles)) {
        abort(403, 'Unauthorized access.');
    }

    return $next($request);
}
}
