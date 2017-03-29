<?php

namespace ifour\LaravelWordpressApi\Providers;

use App\Services\WordpressApi;
use Illuminate\Support\ServiceProvider;

class WordpressApiServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/wp-api.php' => config_path('wp-api.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
     public function register()
 	{
        $this->app->singleton(WordpressApi::class, function ($app) {
            return new WordpressApi(config('wp-api.wordpress'));
        });
    }

    /**
    * Get the services provided by the provider.
    *
    * @return array
    */
    public function provides()
    {
       return [WordpressApi::class];
    }
}
