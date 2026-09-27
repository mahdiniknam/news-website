<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // URL امن webhook: اگر secret تعریف نشده باشد، خودکار ساخته و در env منطقی نگه داشته می‌شود
        URL::macro('baleWebhook', function () {
            $secret = config('services.bale.webhook_secret');

            return rtrim(config('app.url'), '/') . '/bale-webhook/' . $secret;
        });
    }
}
