<?php namespace Creolab\Assets\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class BuildCommand extends BaseCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'assets:build';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Process all registered collections';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$collections = $this->getCollections();

		if ($collections = $this->getCollections())
		{
			app('assets')->clearCache();

			foreach ($collections as $collection)
			{
				if ($collection->count())
				{
					$this->line('Building collection "' . $collection->id . '" to "' . $collection->cacheFile . '"');
					$collection->compile();
					$this->info('Done');
				}
			}
		}
		else
		{
			$this->error('No asset collections registered!');
			$this->line('To register collections, please create a configuration file in "app/config/assets.php".');
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('collection', InputArgument::OPTIONAL, 'Process a specific collection.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
		);
	}

}
