<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectByRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            Auth::check() &&
            Auth::user()->role === 'kasir' &&
            !$request->is('cashier*') &&       // ← pakai wildcard
            !$request->is('admin/login*') &&   // ← jangan redirect saat di halaman login
            !$request->is('admin/logout*')     // ← jangan redirect saat logout
        ) {
            return redirect('/cashier/pos');
        }

        return $next($request);
    }
}
