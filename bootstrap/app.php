<?php

use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\EnsureRoleIs;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('aggregator:poll')->everyFifteenMinutes();
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.admin' => AdminAuth::class,
            'role' => EnsureRoleIs::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            $loginRoute = $request->routeIs('author.*')
                ? route('author.login', ['expired' => 1])
                : route('admin.login', ['expired' => 1]);

            if ($request->expectsJson() || $request->ajax()) {
                return $loginRoute;
            }

            session()->flash('error', 'نشست شما منقضی شده است، لطفا دوباره وارد شوید');

            return $loginRoute;
        });

        $middleware->redirectUsersTo(function (Request $request) {
            return $request->routeIs('author.*')
                ? route('author.show.dashboard')
                : route('admin.show.dashboard');
        });

        $middleware->validateCsrfTokens(except: [
            'bale-webhook/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            return response()->view('errors.404', [], 404);
        });
    })->create();
