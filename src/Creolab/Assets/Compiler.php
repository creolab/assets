<?php namespace Creolab\Assets;

use Assetic\Asset\AssetCache;
use Assetic\Asset\AssetCollection as AsseticCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\StringAsset;
use Assetic\Cache\FilesystemCache;
use Assetic\Filter\LessphpFilter;
use Assetic\Filter\ScssphpFilter;
use Assetic\Filter\CssMinFilter;
use Assetic\Filter\JSMinFilter;

class CompileFailedException extends \Exception {}

class Compiler {

	/**
	 * Illuminate application instance.
	 *
	 * @var Illuminate\Foundation\Application  $app
	 */
	protected $app;

	/**
	 * The collection
	 *
	 * @var string
	 */
	public $collection;

	/**
	 * Compiled contents
	 *
	 * @var string
	 */
	public $contents;

	/**
	 * Create new compiler instance
	 *
	 * @param Assets\AssetCollection $collection
	 */
	public function __construct(&$collection)
	{
		// Set params
		$this->collection = $collection;
	}

	/**
	 * Compiles a given collection
	 * @return void
	 */
	public function run()
	{
		// Collection contents
		$this->collection->contents = '';

		foreach ($this->collection->assets() as $asset)
		{
			if ($asset->type == 'css')
			{
				// Rewrite relative URL's
				$rewriteFilter = new \Creolab\Assets\Filters\CSSRewriteFilter($asset);

				// Dump contents
				$contents = $rewriteFilter->dump($asset);
			}
			else
			{
				// Dump contents
				$contents = $asset->contents();
			}

			// Assign to contents
			$this->collection->contents .= PHP_EOL . $asset->contents();
			$asset->contents = null;
		}

		// Filters
		$filters = array();

		foreach ($this->collection->filters() as $filter)
		{
			$filterClass = app('config')->get('assets::filters.' . $filter);

			if (class_exists($filterClass))
			{
				$filters[] = new $filterClass();
			}
			else
			{
				app('log')->error('[ASSETS] Problem with filter "'.$filter.'". The associated class "'.$filterClass.'" was not found.', array('package' => 'ASSETS', 'module' => 'Compiler'));
			}
		}

		// Create the collection in Assetic
		try
		{
			// Create string asset
			$asset                      = new StringAsset($this->collection->contents, $filters);
			$this->collection->contents = $asset->dump();

			// Clear the cache
			app('assets')->clearCache($this->collection->id);

			// Directory
			$cachePath = public_path() . '/' . app('config')->get('assets::cache_path');
			if ( ! app('files')->exists($cachePath)) app('files')->makeDirectory($cachePath, 0777, true);

			// And write contents
			if ($this->collection->cacheFilePath) app('files')->put($this->collection->cacheFilePath, $this->collection->contents);

			$this->collection->contents = null;
		}
		catch (\Exception $e)
		{
			// echo '<pre>'; print_r($e->getMessage()); echo '</pre>';
			// app('files')->put($this->collection->cacheFilePath, $this->collection->contents());
		}

		// Finished
		app('events')->fire('assets.compiled');

		return $this->collection->contents;
	}

}
