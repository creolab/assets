<?php

return array(

	'compile' => true,

	'freeze' => false,

	'public_path' => 'assets',

	'cache_path'  => 'assets/cache',

	'cache_meta'  => 'assets_meta.json',

	'filters' => array(
		'cssmin'    => '\\Assets\\Filters\\CSSFilter',
		'jsmin'     => '\\Assetic\\Filter\\JSMinFilter',
		'less'      => '\\Assetic\\Filter\\LessphpFilter',
		'scss'      => '\\Assetic\\Filter\\ScssphpFilter',
		'coffee'    => '\\Assetic\\Filter\\CoffeeScriptFilter',
		'cssimport' => '\\Assetic\\Filter\\CssImportFilter',
	),

	'alias' => 'Assets',

);
