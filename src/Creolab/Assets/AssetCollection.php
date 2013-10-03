<?php namespace Creolab\Assets;

class AssetCollection extends \Illuminate\Support\Collection {

	/**
	 * ID of collection
	 * @var array
	 */
	public $id;

	/**
	 * Type of collection
	 * @var array
	 */
	public $type = 'css';

	/**
	 * Should the collection be compiled
	 * @var boolean
	 */
	public $compile = true;

	/**
	 * Should the collection assets be combined into a single file
	 * @var boolean
	 */
	public $combine = true;

	/**
	 * Filters that will be applied to the collection
	 * @var array
	 */
	public $filters = array();

	/**
	 * When was the collection modified
	 * @var integer
	 */
	public $modified;

	/**
	 * Contents of the Collection
	 * @var string
	 */
	public $contents;

	/**
	 * Name of group cache file
	 * @var string
	 */
	public $cacheFile;

	/**
	 * Path for cache file
	 * @var string
	 */
	public $cacheFilePath;

	/**
	 * URL to cached file
	 * @var string
	 */
	public $cacheFileUrl;

	/**
	 * List of all assets
	 * @var array
	 */
	public $items = array();

	/**
	 * Files
	 * @var array
	 */
	public $files = array();

	/**
	 * Create a new collection
	 * @param string $type
	 * @param string $id
	 * @param array  $assets
	 */
	public function __construct($type = 'css', $id, $assets = array(), $options = array())
	{
		// Assign parameters
		$this->id      = $id;
		$this->type    = $type;
		$this->filters = (array) array_get($options, 'filters');
		$this->compile = (bool)  array_get($options, 'compile', true);
		$this->combine = (bool)  array_get($options, 'combine', true);

		// Add asets to collection
		if ($assets) $this->add($type, $assets);
	}

	/**
	 * Add new assets to collection
	 * @param array $assets
	 */
	public function add($type = 'css', $assets = array())
	{
		foreach ($assets as $asset)
		{
			$item = new AssetItem($type, $asset, $this);

			$this->items[] = $item;
			$this->files[] = $asset;
		}

		$this->cacheSetup();
	}

	/**
	 * Return all assets in list
	 * @return array
	 */
	public function assets()
	{
		return $this->items;
	}

	/**
	 * Should the collection be compiled?
	 * @return boolean
	 */
	public function shouldCompile()
	{
		return $this->compile;
	}

	/**
	 * Should the collection be combined?
	 * @return boolean
	 */
	public function shouldCombine()
	{
		return $this->combine;
	}

	/**
	 * Set modification time for collection
	 * @param integer $time
	 */
	public function setModified($time)
	{
		$this->modified = $time;

		// Set new cache name
		$this->cacheSetup();
	}

	/**
	 * Generates a nice cache file name for the collection
	 * @return string
	 */
	public function cacheSetup()
	{
		// Generate the name
		$time      = $this->modified;
		$extension = $this->type;
		$fileName  = pathinfo($this->id, PATHINFO_FILENAME) . "." . md5($this->id . '::' . $time . '::' . implode("|", $this->files) . '::' . implode("|", $this->filters));
		$name      = $fileName . "." . $extension;

		// Generate the cache file path
		$this->cacheFilePath = public_path() . '/' . app('config')->get('assets::cache_path') . '/' . $name;

		// Generate the cache file URL
		$this->cacheFileUrl = app('config')->get('assets::cache_url') . $name;

		return $this->cacheFile = $name;
	}

	/**
	 * Simply return contents of complete collection
	 * @return string
	 */
	public function contents()
	{
		return $this->contents;
	}

	/**
	 * Check if the collection is dirty (needs recompiling)
	 * @return bool
	 */
	public function dirty()
	{
		if ( ! app('files')->exists($this->cacheFilePath))
		{
			return true;
		}

		return false;
	}

	/**
	 * Compile entire collection
	 * @return void
	 */
	public function compile()
	{
		if ($this->dirty() and $this->shouldCompile())
		{
			$compiler       = new Compiler($this);
			$this->contents = $compiler->run();
		}
	}

	/**
	 * Return collection filters
	 * @return array
	 */
	public function filters()
	{
		return $this->filters;
	}

	/**
	 * Display all assets in collection as HTML tags
	 * @return string
	 */
	public function show()
	{
		$responder = new Responder($this);

		return $responder->show();
	}

}
