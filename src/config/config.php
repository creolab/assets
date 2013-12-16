<?php

return array(

	/**
	 * Should the assets be compiled and combined
	 */
	'compile'     => true,

	/**
	 * If set to true, the lib will assume the assets are created
	 * and wont bother to recompile everything.
	 * Can be used for production
	 */
	'freeze'      => false,

	/**
	 * Base URL for application
	 */
	'base_url' => app('url')->to('/') . '/',

	/**
	 * Publicly accessable firectory for your assets
	 */
	'public_dir'  => 'assets',

	/**
	 * A directory to keep your compiled assets
	 */
	'cache_path'  => 'assets/cache',

	/**
	 * Name of the cache meta file
	 */
	'cache_meta'  => 'assets_meta.json',

	/**
	 * Available filters for your assets
	 */
	'filters' => array(
		'cssmin'    => '\\Creolab\\Assets\\Filters\\CSSFilter',
		'jsmin'     => '\\Assetic\\Filter\\JSMinFilter',
		'less'      => '\\Assetic\\Filter\\LessphpFilter',
		'scss'      => '\\Assetic\\Filter\\ScssphpFilter',
		'coffee'    => '\\Assetic\\Filter\\CoffeeScriptFilter',
		'cssimport' => '\\Assetic\\Filter\\CssImportFilter',
	),

	/**
	 * Class alias that will be used for the facade
	 */
	'alias' => 'Assets',

	/**
	 * Debuging
	 */
	'debug' => false,

);
