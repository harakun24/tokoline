<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {

        if (!Auth::guard('karyawan')->check() || Auth::guard('karyawan')->user()->role != $role) {

            // $user = Auth::guard('karyawan')->user();
            // if ($user->role == 3)
            //     return redirect()->route('kelola.panel.admin');
            // else if ($user->role == 1)
            //     return redirect()->route('kelola.panel.cs1');
            // else if ($user->role == 2)
            //     return redirect()->route('kelola.panel.cs2');
            // else
            return redirect()->route('kelola.login.show');
        }
        return $next($request);
    }
}
