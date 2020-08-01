<?php

namespace MTGofa\FawryPay;

use Illuminate\Support\ServiceProvider;

class FawryPayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            // Config file.
            __DIR__.'/config/fawrypay.php' => config_path('fawrypay.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // FawryPay Facede.
        $this->app->singleton('fawrypay', function () {
            return new FawryPay;
        });
    }
}
