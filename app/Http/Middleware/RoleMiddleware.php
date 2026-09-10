<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // orang-tua URL uses dash, role uses underscore
        $normalizedRole = str_replace('-', '_', $role);

        if ($user->role !== $normalizedRole) {
            abort(403, 'Akses ditolak. Role tidak sesuai.');
        }

        return $next($request);
    }
}
