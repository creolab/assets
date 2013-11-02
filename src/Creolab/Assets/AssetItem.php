<?php namespace Creolab\Assets;

use Assetic\Filter\CssRewriteFilter;
use Assetic\Util\CssUtils;
use Assetic\Util\LessUtils;
use Assetic\Util\PathUtils;
use Assetic\Util\VarUtils;

class AssetItem {

	/**
	 * Name of the asset
	 * @var string
	 */
	public $name;

	/**
	 * Assigned to this collection
	 * @var Assets\AssetCollection
	 */
	public $collection;

	/**
	 * Assets type for this file
	 * @var string
	 */
	public $type;

	/**
	 * File path
	 * @var string
	 */
	public $path;

	/**
	 * File URL
	 * @var string
	 */
	public $url;

	/**
	 * Basename of file
	 * @var string
	 */
	public $fileName;

	/**
	 * File extension
	 * @var string
	 */
	public $extension;

	/**
	 * When was the file modified
	 * @var integer
	 */
	public $modified;

	/**
	 * Contents of the asset file
	 * @var string
	 */
	public $contents;

	/**
	 * Create new asset
	 * @param string $type
	 * @param string $name
	 */
	public function __construct($type, $name, &$collection = null)
	{
		// Assign parameters
		$this->type       = $type;
		$this->name       = $name;
		$this->collection = $collection;

		// Read file info
		$this->fileInfo();
	}

	/**
	 * Read asset file info
	 * @return void
	 */
	public function fileInfo()
	{
		// URL to asset file
		$this->url = app('config')->get('assets::base_url') . app('config')->get('assets::public_dir') . '/' . $this->name;

		// Get advanced info for compiling
		if ($this->collection->shouldCompile())
		{
		 	$this->fileAdvancedInfo();
		}
	}

	/**
	 * Get advanced file info only for compiling collections
	 * @return void
	 */
	public function fileAdvancedInfo()
	{
		$public     = public_path() . '/' . app('config')->get('assets::public_path') . '/' . $this->name;
		$this->path = $public;

		if (app('files')->exists($this->path))
		{
			$this->fileName  = basename($this->path);
			$this->extension = app('files')->extension($this->path);
			$this->modified  = (int) @filemtime($this->path);
			$this->dirty     = false;

			// Check for import statements
			$this->contents = $this->contents();

			// Assets directory
			$assetDir = pathinfo($this->name, PATHINFO_DIRNAME);

			// Prepare callback for imports
			$callback = function($matches) use ($assetDir)
			{
				if (isset($matches[2]) and $import = $matches[2])
				{
					$importPath     = public_path() . '/' . app('config')->get('assets::public_path') . '/' . $assetDir . '/' . str_replace("\"", "", $import);
					$importModified = (int) @filemtime($importPath);

					// Check modified time
					if ($importModified > $this->modified) $this->modified = $importModified;

					// Get imported contents
					return app('files')->getRemote($importPath);
				}
			};

			// Test
			$this->contents = CssUtils::filterImports($this->contents, $callback);

			// Update group modified time
			if ($this->modified > $this->collection->modified)
			{
				$this->collection->setModified($this->modified);
			}
		}
	}

	/**
	 * Read file contents
	 * @return string
	 */
	public function contents()
	{
		if ($this->path and ! $this->contents)
		{
			if (app('files')->exists($this->path))
			{
				$this->contents = app('files')->getRemote($this->path);
			}
		}

		return $this->contents;
	}

	/**
	 * Set rewritten contents
	 * @param string $contents
	 */
	public function setContents($contents)
	{
		$this->contents = $contents;
	}

	/**
	 * Return public URL for assets
	 * @return string
	 */
	public function getSourceRoot()
	{
		return app('url')->to('/');
	}

	/**
	 * Return source relative path for asset
	 * @return string
	 */
	public function getSourcePath()
	{
		// Remove public path
		$publicPath = app()->make('path.public') . '/';
		$path       = str_replace($publicPath, '', $this->path);

		return $path;
	}

	/**
	 * Return target relative path for asset
	 * @return string
	 */
	public function getTargetPath()
	{
		// Remove public path
		$dir        = pathinfo($this->collection->cacheFilePath, PATHINFO_DIRNAME);
		$publicPath = app()->make('path.public') . '/';
		$path       = str_replace($publicPath, '', $dir);

		return $path;
	}

}
