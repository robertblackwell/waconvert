<?php
namespace WaConvert;

use WaConvert\Config;

/**
* add ox11 entries to "theamericas" trip. These have been stored locally for convenience.
* update the americas entries to have correct vehicle
*/
class TheAmericasUpdate
{
	/**
	* Constructor
	* @param Config $config Parameters for this run of the command.
	* @return void
	*/
	public function __construct(Config $config)
	{
		$this->config = $config;
	}
	/** @return void */
	public function convert()
	{
		$c = new TheAmericasCopyOx11Entries($this->config);
		$c->convert();
		$n = new TheAmericasAddVehicle($this->config);
		$n->convert();
	}
}
