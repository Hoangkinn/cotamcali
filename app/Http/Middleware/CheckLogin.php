<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        \Log::info('SESSION CHECK:', session()->all());

        if (!session()->has('user') || !session()->has('token')) {
            \Log::info('SESSION LOST, redirecting to login');
            return redirect()->route('login.form')->withErrors([
                'login' => 'Vui lòng đăng nhập để tiếp tục.'
            ]);
        }

        return $next($request);
    }

}
