<?php namespace Creolab\Assets\Filters;

use Assetic\Filter\BaseCssFilter;
use Creolab\Assets\AssetItem;

class CSSRewriteFilter {

	const REGEX_URLS = '/url\((["\']?)(?P<url>.*?)(\\1)\)/';

	/**
	 * The asset that need to be filtered
	 * @var AssetItem
	 */
	protected $asset;

	/**
	 * Init filter
	 * @param AssetItem $asset
	 */
	public function __construct(AssetItem $asset)
	{
		$this->asset = $asset;
	}

	/**
	 * Dump contents after rewriting
	 * @return string
	 */
	public function dump()
	{
		$sourceBase = $this->asset->getSourceRoot();
		// echo '<pre>'; print_r(var_dump($sourceBase)); echo '</pre>';
		// echo '<pre>'; print_r("************************************************************************************************"); echo '</pre>';

		$sourcePath = $this->asset->getSourcePath();
		// echo '<pre>'; print_r(var_dump($sourcePath)); echo '</pre>';
		// echo '<pre>'; print_r("************************************************************************************************"); echo '</pre>';

		$targetPath = $this->asset->getTargetPath();
		// echo '<pre>'; print_r(var_dump($targetPath)); echo '</pre>';
		// echo '<pre>'; print_r("************************************************************************************************"); echo '</pre>';

		if (null === $sourcePath || null === $targetPath || $sourcePath == $targetPath) {
			return;
		}

		// learn how to get from the target back to the source
		if (false !== strpos($sourceBase, '://')) {
			list($scheme, $url) = explode('://', $sourceBase.'/'.$sourcePath, 2);
			list($host, $path) = explode('/', $url, 2);

			$host = $scheme.'://'.$host.'/';
			$path = false === strpos($path, '/') ? '' : dirname($path);
			$path .= '/';
		} else {
			// assume source and target are on the same host
			$host = '';

			// pop entries off the target until it fits in the source
			if ('.' == dirname($sourcePath)) {
				$path = str_repeat('../', substr_count($targetPath, '/'));
			} elseif ('.' == $targetDir = dirname($targetPath)) {
				$path = dirname($sourcePath).'/';
			} else {
				$path = '';
				while ($targetDir and 0 !== strpos($sourcePath, $targetDir)) {
					if (false !== $pos = strrpos($targetDir, '/')) {
						$targetDir = substr($targetDir, 0, $pos);
						$path .= '../';
					} else {
						$targetDir = '';
						$path .= '../';
						break;
					}
				}
				$path .= ltrim(substr(dirname($sourcePath).'/', strlen($targetDir)), '/');
			}
		}

		$contents = $this->filterUrls($this->asset->contents(), function($matches) use ($host, $path) {
			if (false !== strpos($matches['url'], '://') || 0 === strpos($matches['url'], '//') || 0 === strpos($matches['url'], 'data:')) {
				// absolute or protocol-relative or data uri
				return $matches[0];
			}

			if (isset($matches['url'][0]) && '/' == $matches['url'][0]) {
				// root relative
				return str_replace($matches['url'], $host.$matches['url'], $matches[0]);
			}

			// document relative
			$url = $matches['url'];

			while (0 === strpos($url, '../') && 2 <= substr_count($path, '/')) {
				$path = substr($path, 0, strrpos(rtrim($path, '/'), '/') + 1);
				$url = substr($url, 3);
			}

			$parts = array();
			foreach (explode('/', $host.$path.$url) as $part) {
				if ('..' === $part && count($parts) && '..' !== end($parts)) {
					array_pop($parts);
				} else {
					$parts[] = $part;
				}
			}

			return str_replace($matches['url'], implode('/', $parts), $matches[0]);
		});

		// Set contents in asset object
		$this->asset->setContents($contents);

		return $contents;
	}

	/**
	 * Filters all CSS url()'s through a callable.
	 *
	 * @param string   $content  The CSS
	 * @param callable $callback A PHP callable
	 * @param integer  $limit    Limit the number of replacements
	 * @param integer  $count    Will be populated with the count
	 *
	 * @return string The filtered CSS
	 */
	public static function filterUrls($content, $callback, $limit = -1, &$count = 0)
	{
		return preg_replace_callback(static::REGEX_URLS, $callback, $content, $limit, $count);
	}

}
