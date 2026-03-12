<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;
class UserRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            abort(403, 'You do not have permission to access this page.');
        }

        $allowed = collect($roles)
            ->flatMap(fn($roleEntry) => preg_split('/[|,]/', (string) $roleEntry))
            ->map(fn($rolePart) => trim($rolePart))
            ->filter();

        if ($allowed->isEmpty()) {
            abort(403, 'You do not have permission to access this page.');
        }

        if ($allowed->contains(Auth::user()->role?->slug)) {
            return $next($request);
        }
        abort(403, 'You do not have permission to access this page.');
    }
}
