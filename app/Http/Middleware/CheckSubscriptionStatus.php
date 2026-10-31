<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Admin is immune to subscription lock
            if ($user->role === \App\Enums\UserRole::Admin) {
                return $next($request);
            }

            // Check if subscription has expired using effective expiry (handles Prodi inheriting Perti's expiry)
            if ($user->effective_package_valid_until && $user->effective_package_valid_until < now()) {
                
                $isModifyingRequest = in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']);
                
                $blockedGetRoutes = [
                    'perti.prodis.create',
                    'perti.prodis.edit',
                    'perti.reports.pdf',
                    'unit.reports.pdf'
                ];
                $isBlockedGet = $request->method() === 'GET' && in_array($request->route()?->getName(), $blockedGetRoutes);

                // Block any modifying requests or specific GET requests
                if ($isModifyingRequest || $isBlockedGet) {
                    
                    // Allow essential routes to pass through (only relevant for POST/PUT/DELETE)
                    $allowedRoutes = [
                        'logout',
                        'profile.update',
                        'profile.destroy',
                        'checkout.process',
                        'upgrade.process'
                    ];

                    if (!in_array($request->route()?->getName(), $allowedRoutes)) {
                        // For API/JSON requests (if any)
                        if ($request->expectsJson()) {
                            return response()->json(['message' => 'Tindakan diblokir (Read-Only). Masa langganan Anda telah habis.'], 403);
                        }
                        
                        // For normal web requests
                        return redirect()->back()->with('error', 'Tindakan diblokir (Read-Only). Masa langganan Anda telah habis, silakan perpanjang untuk mengedit atau menambahkan data.');
                    }
                }
            }
        }

        return $next($request);
    }
}
