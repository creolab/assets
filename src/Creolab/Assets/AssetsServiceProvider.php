<?php namespace Creolab\Assets;

use Illuminate\Support\ServiceProvider;

/**
 * Service provider for Assets package
 * @author Boris Strahija <boris@creolab.hr>
 */
class AssetsServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 * @return void
	 */
	public function boot()
	{
		// Register the package
		$this->package('creolab/assets', 'assets', __DIR__.'/../../');

		// Register IoC bindings
		$this->registerBindings();

		// Add some Artisan commands
		$this->bootCommands();

		// Shortcut so developers don't need to add an Alias in app/config/app.php
		if ($alias = $this->app['config']->get('assets::alias'))
		{
			$this->app->booting(function() use ($alias)
			{
				$loader = \Illuminate\Foundation\AliasLoader::getInstance();

				$loader->alias($alias, '\Creolab\Assets\AssetsFacade');
			});
		}

		// View helpers
		$this->registerBladeExtensions();

		// Include various files
		require __DIR__ . '/../../helpers.php';
	}

	/**
	 * Register the service provider.
	 * @return void
	 */
	public function register()
	{
	}

	/**
	 * Register all available commands
	 * @return void
	 */
	public function bootCommands()
	{
		// Add list command to IoC
		$this->app['assets.commands.assets'] = $this->app->share(function($app) {
			return new Commands\AssetsCommand($app);
		});

		// Add build command to IoC
		$this->app['assets.commands.build'] = $this->app->share(function($app) {
			return new Commands\BuildCommand($app);
		});

		// Now register all the commands
		$this->commands('assets.commands.assets', 'assets.commands.build');
	}

	/**
	 * Register IoC bindings
	 * @return void
	 */
	public function registerBindings()
	{
		$this->app->singleton('assets', function($app)
		{
			return new Assets($app);
		});
	}

	/**
	 * Some helpers to use in our Blade templates
	 * @return void
	 */
	public function registerBladeExtensions()
	{
		// Get instance of Blade compiler
		$blade = $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler();

		// Dump assets collection
		$blade->extend(function($value, $compiler)
		{
			$matcher = $compiler->createMatcher('assets');

			return preg_replace($matcher, '$1<?php echo assets$2; ?>', $value);
		});

		// Dump JS assets
		$blade->extend(function($value, $compiler)
		{
			$matcher = $compiler->createMatcher('js_assets');

			return preg_replace($matcher, '$1<?php echo js_assets$2; ?>', $value);
		});

		// Dump CSS assets
		$blade->extend(function($value, $compiler)
		{
			$matcher = $compiler->createMatcher('css_assets');

			return preg_replace($matcher, '$1<?php echo css_assets$2; ?>', $value);
		});
	}

}
