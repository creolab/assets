<?php namespace Creolab\Assets;

class Responder {

	/**
	 * The collection
	 * @var string
	 */
	public $collection;

	/**
	 * Create a new assets responder instance
	 * @param Assets\AssetCollection $collection
	 */
	public function __construct($collection)
	{
		// Assign params
		$this->collection = $collection;

		// Compile if needed
		$this->collection->compile();
	}

	/**
	 * Create single HTML tag
	 * @param  string $url
	 * @return string
	 */
	public function tag($url)
	{
		if ($this->collection->type == 'css')
		{
			return '<link rel="stylesheet" href="' . $url . '">' . PHP_EOL;
		}
		elseif ($this->collection->type == 'js')
		{
			return '<script src="' . $url . '"></script>' . PHP_EOL;
		}
	}

	/**
	 * Generates a HTML tag
	 * @return string
	 */
	public function tags()
	{
		$tags = '';

		if ($this->collection->shouldCombine())
		{
			$tags = $this->tag($this->collection->cacheFileUrl);
		}
		else
		{
			foreach ($this->collection->items as $asset)
			{
				$tags .= $this->tag($asset->url);
			}
		}

		return $tags;
	}

	/**
	 * Display HTML tags for assets collection
	 *
	 * @return string
	 */
	public function show()
	{
		echo $this->tags();
	}

}
