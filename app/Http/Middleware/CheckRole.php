<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = session('user'); 

        // dd($user);
        if (!$user) {
            return redirect()->route('login.form');
        }
        if (in_array($user['Roles'], $roles)) {
            return $next($request);
        }
        // // Nếu role của user không nằm trong roles được phép
        // if (!in_array($user->Roles, $roles)) {
        //     abort(403, 'Bạn không có quyền truy cập.');
        // }

        return $next($request);
    }
}
