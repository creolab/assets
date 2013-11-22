<?php namespace Creolab\Assets\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Composer;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
* Assets console commands
* @author Boris Strahija <bstrahija@gmail.com>
*/
abstract class BaseCommand extends Command {

	/**
	 * List of all available asset collections
	 * @var array
	 */
	protected $collections;

	/**
	 * IoC
	 * @var Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * DI
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		parent::__construct();
		$this->app = $app;
	}

	/**
	 * Get the console command options.
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

	/**
	 * Return all asset collections
	 * @return array
	 */
	public function getCollections()
	{
		$collections = app('config')->get('assets', 'assets::collections');

		// Add collections to asset environment
		if ($collections)
		{
			foreach ($collections as $name => $collection)
			{
				// Get settings
				$type    = array_get($collection, 'type', 'css');
				$assets  = array_get($collection, 'assets', 'css');
				$options = array_except($collection, 'assets');

				// Add the collections
				app('assets')->addCollection(array_get($collection, 'type'), $name, array_get($collection, 'assets'), $options);
			}
		}

		// Prepare results
		$result = array();
		foreach (app('assets')->collections() as $name => $collection)
		{
			if ($collection->count()) $result[$name] = $collection;
		}

		return $result;
	}
}
