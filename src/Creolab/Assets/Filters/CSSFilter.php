<?php namespace Creolab\Assets\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Creolab\Assets\Minifiers\CSSMin;

class CSSFilter implements FilterInterface {

	public function filterLoad(AssetInterface $asset)
	{

	}

	public function filterDump(AssetInterface $asset)
	{
		$content = $asset->getContent();
		$min = new CSSmin();

		$asset->setContent($min->run($content));
	}

}
