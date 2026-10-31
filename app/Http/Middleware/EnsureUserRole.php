<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $allowed = collect($roles)
            ->flatMap(fn (string $r) => explode(',', $r))
            ->map(fn (string $r) => trim($r))
            ->filter()
            ->map(fn (string $r) => UserRole::tryFrom($r))
            ->filter();

        if ($allowed->isEmpty() || ! $allowed->contains($user->role)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
