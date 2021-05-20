<?php
namespace WithKao\Providers;

use WithKao\LineNotify;
use Illuminate\Support\ServiceProvider;

class LineNotifyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/line-notify.php' => config_path('line-notify.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/line-notify.php', 'line-notify');

        $this->app->singleton(LineNotify::class, function () {
            $token = config('line-notify.access_token');
            return new LineNotify($token);
        });
    }
}
