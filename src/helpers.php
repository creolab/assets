<?php

if ( ! function_exists('assets_collection'))
{
	/**
	 * Add assets to collection
	 * @param  string $collection
	 * @param  array  $assets
	 * @param  array  $filters
	 * @return string
	 */
	function assets_collection($collection = 'default.css', $assets = array(), $options = array())
	{
		$type = pathinfo($collection, PATHINFO_EXTENSION);
		$name = pathinfo($collection, PATHINFO_FILENAME);

		return app('assets')->addCollection($type, $name, $assets, $options);
	}
}

if ( ! function_exists('css_assets'))
{
	/**
	 * Display tags for specific CSS collection (optionally add new assets)
	 * @param  string $collection
	 * @param  array  $assets
	 * @param  array  $filters
	 * @return string
	 */
	function css_assets($collection = 'default', $assets = array(), $options = array())
	{
		return app('assets')->showCSSCollection($collection, $assets, $options);
	}
}

if ( ! function_exists('js_assets'))
{
	/**
	 * Display tags for specific JS collection (optionally add new assets)
	 * @param  string $collection
	 * @param  array  $filters
	 * @return string
	 */
	function js_assets($collection = 'default', $assets = array(), $options = array())
	{
		return app('assets')->showJSCollection($collection, $assets, $options);
	}
}

if ( ! function_exists('assets'))
{
	/**
	 * Display tags for specific collection (autodetect type and optionally add new assets)
	 * @param  string $collection
	 * @param  array  $assets
	 * @param  array  $filters
	 * @return string
	 */
	function assets($collection = 'default', $assets = array(), $options = array())
	{
		if ( ! $assets)
		{
			$configCollection = str_replace(".", "_", $collection);
			$config           = app('config')->get('assets.'.$configCollection, 'krustr::assets.'.$configCollection);

			if ($config and is_array($config))
			{
				$assets  = array_get($config, 'assets');
				$options = array_except($config, 'assets');

				if ( ! $name = array_get($options, 'name')) $options['name'] = $collection;
			}
		}
		elseif (array_get($assets, 'assets'))
		{
			$assets          = array_get($assets, 'assets');
			$options         = array_except($assets, 'assets');
			$options['name'] = $collection;
		}

		return app('assets')->assets($collection, $assets, $options);
	}
}
