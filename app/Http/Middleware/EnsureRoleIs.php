<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * محدودسازی دسترسی بر اساس نقش.
 *
 * استفاده: ->middleware('role:super-admin')  یا  'role:admin,super-admin'
 * اگر کاربر هیچ‌کدام از نقش‌های داده‌شده را نداشت → 403.
 * کاربر super-admin همیشه مجاز است.
 */
class EnsureRoleIs
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::guard('admin')->user();

        if (! $user) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'نشست شما منقضی شده است'], 401);
            }

            return redirect()->route('admin.login', ['expired' => 1])
                ->with('error', 'نشست شما منقضی شده است، لطفا دوباره وارد شوید');
        }

        if ($user->hasRole('super-admin')) {
            return $next($request);
        }

        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        abort(403, 'شما دسترسی به این بخش را ندارید.');
    }
}
