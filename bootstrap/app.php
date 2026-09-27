<?php

use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\EnsureRoleIs;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.admin' => AdminAuth::class,
            'role' => EnsureRoleIs::class,
        ]);

        //
        // وقتی سشن منقضی می‌شود، middleware «auth» لاراول مهمان را به route('login')
        // می‌فرستد که در این پروژه وجود ندارد (فقط admin.login / author.login داریم)
        // و خطای «Route [login] not defined» ظاهر می‌شد.
        // الان مهمان به صفحه لاگین پنل خودش هدایت می‌شود.
        //
        $middleware->redirectGuestsTo(function (Request $request) {
            return $request->routeIs('author.*')
                ? route('author.login')
                : route('admin.login');
        });

        // کاربر لاگین‌شده‌ای که سراغ صفحه لاگین رفت، مستقیم به پنل خودش برود.
        $middleware->redirectUsersTo(function (Request $request) {
            return $request->routeIs('author.*')
                ? route('author.show.dashboard')
                : route('admin.show.dashboard');
        });

        // Webhook ربات بله از CSRF مستثناست (امنیت آن با secret در URL تضمین می‌شود)
        $middleware->validateCsrfTokens(except: [
            'bale-webhook/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
