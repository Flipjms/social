<?php namespace Clumsy\Social;

use Illuminate\Support\ServiceProvider;
use Clumsy\Social\Providers\Facebook\Console\UpdateLikes;

class SocialServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$path = __DIR__.'/../..';

        $this->package('clumsy/social', 'clumsy/social', $path);

        // Register artisan commands:
        // Facebook UpdateLikes
        $this->app['command.clumsy.social.updatelikes'] = $this->app->share(function($app){
				return new UpdateLikes();
	        }
        );
        $this->commands('command.clumsy.social.updatelikes');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
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
