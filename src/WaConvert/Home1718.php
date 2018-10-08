<?php
namespace WaConvert;

use WaConvert\Config;

/**
* Removes the rtw entry home17-18
*/
class Home1718
{
	/**
	* Constructor
	* @param Config $config Details of parameters for this run.
	* @return void
	*/
	public function __construct(Config $config)
	{
		$this->config = $config;
	}
	/** @return void */
	public function convert()
	{
		$locator = \Database\Locator::get_instance();
		$h1718 = $locator->item_dir("rtw", "home17-18");
		$dryrun = $this->config->dryrun;
		if (!$dryrun) {
			$this->config->output->writeln("rm -R {$h1718}");
		} else {
			system("rm -R {$h1718}");
		}
	}
}
