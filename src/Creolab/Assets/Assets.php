<?php namespace Creolab\Assets;

use Illuminate\Foundation\Application;

class Assets {

	/**
	 * All collections
	 * @var array
	 */
	public $collections = array();

	/**
	 * Simple configuration
	 * @var array
	 */
	protected $config = array();

	/**
	 * IoC
	 * @var Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * Initialize package
	 * @param  Illuminate\Foundation\Application  $app
	 */
	public function __construct(Application $app)
	{
		$this->app = $app;

		// Fetch configuration
		$this->configure();

		// Add default collections
		$this->addDefaultCollections();
	}

	/**
	 * Create default asset collections for ease of use
	 * @return void
	 */
	protected function addDefaultCollections()
	{
		if ( ! $this->collectionExists('default.css')) $this->addCSSCollection('default');
		if ( ! $this->collectionExists('default.js'))  $this->addJSCollection('default');
	}

	/**
	 * Create new asset collection
	 */
	public function addCollection($type = 'css', $id, $assets = array(), $options = array())
	{
		// Create name
		$name = array_get($options, 'name') ?: $id . '.' . $type;

		// Add new assets collection if it doesn't exist or just add additional assets
		if ( ! $this->collectionExists($name))
		{
			$this->collections[$name] = new AssetCollection($type, $name, $assets, $options);
		}
		else
		{
			$this->collections[$name]->add($assets);
		}
	}

	/**
	 * Create new CSS collection
	 */
	public function addCSSCollection($id, $assets = array(), $options = array())
	{
		return $this->addCollection('css', $id, $assets, $options);
	}

	/**
	 * Create new JS collection
	 */
	public function addJSCollection($id, $assets = array(), $options = array())
	{
		return $this->addCollection('js', $id, $assets, $options);
	}

	/**
	 * Show collection tags
	 * @param  string $type
	 * @param  string $id
	 * @param  array  $assets
	 * @return string
	 */
	public function showCollection($type = 'css', $id, $assets = array(), $options = array())
	{
		// Create name
		$name = $id . '.' . $type;

		// Do we add new assets to collection?
		if ($assets)
		{
			$this->addCollection($type, $id, $assets, $options);
		}

		// Output
		if ($this->collectionExists($name)) return $this->collection($name)->show();
	}

	/**
	 * Show CSS collection tags
	 * @param  string $id
	 * @param  array  $assets
	 * @return string
	 */
	public function showCSSCollection($id, $assets = array(), $options = array())
	{
		return $this->showCollection('css', $id, $assets, $options);
	}

	/**
	 * Just a shortcut to show a CSS collection
	 * @param  string $id
	 * @param  array  $assets
	 * @param  array  $filters
	 * @return string
	 */
	public function css($id, $assets = array(), $options = array())
	{
		return $this->showCSSCollection($id, $assets, $options);
	}

	/**
	 * Show JS collection tags
	 * @param  string $id
	 * @param  array  $assets
	 * @return string
	 */
	public function showJSCollection($id, $assets = array(), $options = array())
	{
		return $this->showCollection('js', $id, $assets, $options);
	}

	/**
	 * Just a shortcut to show a JS collection
	 * @param  string $id
	 * @param  array  $assets
	 * @param  array  $filters
	 * @return string
	 */
	public function js($id, $assets = array(), $options = array())
	{
		return $this->showJSCollection($id, $assets, $options);
	}

	/**
	 * Fetch CDN assets
	 * @return string
	 */
	public function cdn($id)
	{

	}

	/**
	 * Check if a collection exists
	 * @param  string  $id
	 * @return boolean
	 */
	public function collectionExists($id)
	{
		return isset($this->collections[$id]);
	}

	/**
	 * Get specific collection
	 * @param  string $id
	 * @return Assets\AssetCollection
	 */
	public function collection($id)
	{
		return isset($this->collections[$id]) ? $this->collections[$id] : null;
	}

	/**
	 * Return all collection objectes
	 * @return array
	 */
	public function collections()
	{
		return $this->collections;
	}

	/**
	 * Display tags for specific collection (autodetect type and optionally add new assets)
	 * @param  string $collection
	 * @param  array  $assets
	 * @param  array  $filters
	 * @return string
	 */
	public function assets($collection = 'default', $assets = array(), $options = array())
	{
		$start = microtime();
		$type = pathinfo($collection, PATHINFO_EXTENSION);
		$name = pathinfo($collection, PATHINFO_FILENAME);

		if ($type == 'js') $assets = app('assets')->showJSCollection($name,  $assets, $options);
		else               $assets = app('assets')->showCSSCollection($name, $assets, $options);

		// Calculate duration
		list($sm, $ss) = explode(' ', $start);
		list($em, $es) = explode(' ', microtime());
		$duration = number_format(($em + $es) - ($sm + $ss), 4);
		app('log')->debug("[ASSETS] Collection '$collection' displayed in $duration ms.");

		return $assets;
	}

	/**
	 * Read and prepare configuration
	 * @return array
	 */
	public function configure($options = array())
	{
		app('config')->set('assets::base_url',  app('url')->asset('/'));
		app('config')->set('assets::cache_url', app('url')->asset(app('config')->get('assets::cache_path')) . '/');
	}

	/**
	 * Clear all cache, or only the cache for a collection
	 * @param  string $collection
	 * @return void
	 */
	public function clearCache($collection = null)
	{
		// Get list of cached files
		$path  = public_path() . '/' . app('config')->get('assets::cache_path');
		$files = app('files')->files($path);

		if ($files)
		{
			foreach ($files as $file)
			{
				// Delete by group
				if ($collection)
				{
					$target = pathinfo($collection);
					$info   = pathinfo($file);

					if (strpos($info['filename'], $target['filename']) === 0 and $info['extension'] === $target['extension'])
					{
						app('files')->delete($file);
					}
				}
				else
				{
					app('files')->delete($file);
				}
			}
		}
	}

	/**
	 * Returns a public path to the application
	 * @param  string $path
	 * @return string
	 */
	public function publicPath($path = null)
	{
		return app('config')->get('assets::base_url');
	}

	/**
	 * Path to assets directory
	 * @return string
	 */
	public function assetsPath()
	{
		return $this->publicPath() . app('config')->get('assets::public_dir');
	}

}
