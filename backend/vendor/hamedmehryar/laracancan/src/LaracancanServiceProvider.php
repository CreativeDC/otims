<?php namespace Hamedmehryar\Laracancan;

use Hamedmehryar\Laracancan\Commands\MigrationCommand;
use Hamedmehryar\Laracancan\Commands\SeederCommand;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class LaracancanServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->publishes([
			base_path('vendor/hamedmehryar/laracancan/src/config/config.php') => config_path('laracancan.php')
		]);
		$this->publishes([
			__DIR__.'/public' => public_path('hamedmehryar/laracancan'),
		], 'public');

		if (! $this->app->routesAreCached()) {
			require __DIR__.'/routes.php';
		}
		$this->loadViewsFrom(__DIR__.'/views', 'laracancan');

		// Register commands
		$this->commands('command.laracancan.migration');
		$this->commands('command.laracancan.seed');
        //blade tags for laracancan
        Blade::directive('canRead', function($expression) {
            return "<?php if(laracancan::canRead($expression)):?>";
        });
        Blade::directive('endcanRead', function($expression)
        {
            return '<?php endif; ?>';
        });
        Blade::directive('canCreate', function($expression) {
            return "<?php if(laracancan::canCreate($expression)):?>";
        });
        Blade::directive('endcanCreate', function($expression)
        {
            return '<?php endif; ?>';
        });
        Blade::directive('canUpdate', function($expression) {
            return "<?php if(laracancan::canUpdate($expression)):?>";
        });
        Blade::directive('endcanUpdate', function($expression)
        {
            return '<?php endif; ?>';
        });
        Blade::directive('canDelete', function($expression) {
            return "<?php if(laracancan::canDelete($expression)):?>";
        });
        Blade::directive('endcanDelete', function($expression)
        {
            return '<?php endif; ?>';
        });

	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->mergeConfigFrom(
			base_path('vendor/hamedmehryar/laracancan/src/config/config.php'), 'laracancan'
		);

		$this->registerLaracancan();
		$this->registerCommands();
	}

	/**
	 * Register the application bindings.
	 *
	 * @return void
	 */
	private function registerLaracancan()
	{
		$this->app->bind('laracancan', function ($app) {
			return new Laracancan($app);
		});
	}

	/**
	 * Register the artisan commands.
	 *
	 * @return void
	 */
	private function registerCommands()
	{
		$this->app->singleton('command.laracancan.migration', function ($app) {
			return new MigrationCommand();
		});
		$this->app->singleton('command.laracancan.seed', function ($app) {
			return new SeederCommand();
		});
	}

	/**
	 * Get the services provided.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [
			'command.laracancan.migration',
			'command.laracancan.seed'
		];
	}

}
