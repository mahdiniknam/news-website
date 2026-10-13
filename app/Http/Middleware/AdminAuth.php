<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'نشست شما منقضی شده است'], 401);
            }

            return redirect()->route('admin.login', ['expired' => 1])
                ->with('error', 'نشست شما منقضی شده است، لطفا دوباره وارد شوید');
        }

        return $next($request);
    }
}
