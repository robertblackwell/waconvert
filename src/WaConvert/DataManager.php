<?php
namespace WaConvert;

use WaConvert\Config;

/**
* Operates in a project hierachy of whiteacorn or test.whiteacorn.
* saves a copy of the doc_root/data -> doc_root/data_clean
* restores doc_root/data FROM doc_root/data_clean
*/
class DataManager
{
	private $config;
	private $locator;
	private $data_root;
	private $doc_root;
	private $saved_data_root;
	/**
	* @param Config $config Config details for this run of the command.
	* @return DataManager
	*/
	public function __construct(Config $config)
	{
		$this->config = $config;
		$this->locator = \Database\Locator::get_instance();
		$this->doc_root = $this->locator->doc_root();
		$this->saved_data_root = $config->dataRootSavePath;
		$this->data_root = $config->dataRoot;
	}
	/**
	* Save a copy of the $doc_root/data folder in $doc_root/data_clean
	* @return void
	*/
	public function save_data()
	{
		$data_root = $this->locator->data_root;
		
		if (!$this->config->dryrun) {
			system("cp -r {$data_root}/ {$this->saved_data_root}");
		} else {
			$this->config->output->writeln("cp -r {$data_root}/ {$this->saved_data_root}");
		}
	}
	/**
	* Restore the $doc_root/data folder from the saved copy at $doc_root/data_clean
	* @return void
	*/
	public function restore_from_saved()
	{
		assert(false);
		$data_root = $this->locator->data_root;
		print "cp -r {$this->clean_data_root}/ {$this->doc_root}\n";
		// system("cp -r {$this->clean_data_root}/ {$this->doc_root}\n");
	}
	/**
	* @return void
	*/
	public function cleanup()
	{
	}
}
