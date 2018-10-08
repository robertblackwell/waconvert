<?php
namespace WaConvert;

use WaConvert\Config;

// add vehicle gxv to all entries
class RtwAddVehicle
{
	private $config;
	/**
	* @param Config $config Config details.
	* @return RtwAddVehicle
	*/
	public function __construct(Config $config)
	{
		$this->config = $config;
	}
	function convert()
	{
		// now change trip and add vehicle
		$changer = new TripVehicleChanger($this->config);
		$changer->inplace_change_trip_vehicle("rtw", "rtw", "rtw", "gxv");
	}
}
