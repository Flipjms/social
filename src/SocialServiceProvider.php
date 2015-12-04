<?php

namespace Clumsy\Social;

use Illuminate\Support\ServiceProvider;
use Clumsy\Social\Providers\Facebook\Console\UpdateLikes;
use Clumsy\Social\Providers\Facebook\Console\UpdatePosts;

class SocialServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/config.php', 'clumsy.social');
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/config.php' => config_path('clumsy/social.php'),
        ], 'config');

        // Register artisan commands:
        // Facebook UpdateLikes
        $this->app['command.clumsy.social.updatelikes'] = $this->app->share(function ($app) {
                return new UpdateLikes();
        });
        $this->commands('command.clumsy.social.updatelikes');

        $this->app['command.clumsy.social.updateposts'] = $this->app->share(function ($app) {
                return new UpdatePosts();
        });
        $this->commands('command.clumsy.social.updateposts');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }
}
