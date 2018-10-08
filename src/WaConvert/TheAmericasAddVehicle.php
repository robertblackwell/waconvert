<?php
namespace WaConvert;

use WaConvert\Config;

// americas - add vehicle = tiger to all entries
class TheAmericasAddVehicle
{
	/**
	* @param Config $config Details for this run of command.
	* @return void
	*/
	public function __construct(Config $config)
	{
		$this->config = $config;
	}
	/** @return void */
	public function convert()
	{
		// now change trip and add vehicle
		$changer = new TripVehicleChanger($this->config);
		$changer->inplace_change_trip_vehicle("theamericas", "theamericas", "theamericas", "tiger");
	}
}
