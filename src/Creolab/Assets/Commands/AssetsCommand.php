<?php namespace Creolab\Assets\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AssetsCommand extends BaseCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'assets';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Display all registered asset collections.';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		if ($collections = $this->getCollections())
		{
			$this->displayCollections($collections);
		}
		else
		{
			$this->error('No asset collections registered!');
			$this->line('To register collections, please create a configuration file in "app/config/assets.php".');
		}
	}

	/**
	 * Display a list of collections
	 *
	 * @param  array $collections
	 * @return void
	 */
	public function displayCollections($collections)
	{
		// Get table helper
		$this->table = $this->getHelperSet()->get('table');
		$headers = array('Name', 'Path', 'Files', 'Combine', 'Filters');

		// Add collections to table rows
		$rows = array();
		foreach ($collections as $name => $collection)
		{
			$this->info($collection->cacheFilePath);
			$rows[] = array(
				$name,
				($path = parse_url($collection->cacheFileUrl, PHP_URL_PATH)) ? $path : '-',
				$collection->count(),
				$collection->combine ? 'Yes' : 'No',
				($filters = $collection->filters) ? implode(", ", $filters) : '-',
			);
		}

		// Set header and render table
		$this->table->setHeaders($headers)->setRows($rows);
		$this->table->render($this->getOutput());
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}
