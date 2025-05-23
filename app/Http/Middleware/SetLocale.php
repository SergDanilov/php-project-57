<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasCookie('locale')) {
            app()->setLocale($request->cookie('locale'));
        } elseif (session()->has('locale')) {
            app()->setLocale(session()->get('locale'));
        }
        return $next($request);
    }
}
