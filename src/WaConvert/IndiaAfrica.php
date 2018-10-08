<?php
namespace WaConvert;

use WaConvert\Config;

//
// Change the vehicle for the africa and india segments of the rtw trip to no_vehicle
//
//
class IndiaAfrica
{
	/**
	* @var array $africa_list List of Entries in "rtw" that need to have vehicle changed to  "novehicle",
	*                         these entries remain in rtw.
	*/
	public static $africa_list = [
		"140824",
		"140830",
		"140831",
		"140901",
		"140902",
		"140903",
		"140904",
		"140905",
		"140906",
		"140907",
		"140908",
		"140909",
		"140910",
		"140911",
		"140912",
		"140913",
		"140914",
		"140915",
		"140916",
		"140917",
		"140918",
		"140919",
		"140921",
		"140922",
		"140925",
	];
	/**
	* @var array $india_list List of Entries in "rtw" that need to have vehicle changed to novehicle.
	*                        Note these entries continue to be in rtw.
	*/
	public static $india_list = [
		"141124",
		"141125",
		"141126",
		"141127",
		"141128",
		"141129",
		"141130",
		"141201",
		"141202",
		"141203",
		"141204",
		"141205",
		"141206",
		"141207",
	];
	/**
	* @param Config $config Config details for this run.
	* @return IndiaAfrica
	*/
	public function __construct(Config $config)
	{
		$this->config = $config;
	}
	/**
	* Change the vehicle for these entries from gxv to no_vehicle. Must be performed AFTER
	* rtw has had vehicle added.
	* @return void
	*/
	public function convert()
	{

		$changer = new TripVehicleChanger($this->config);
		$changer->inplace_list_substitute_vehicle("rtw", self::$africa_list, "gxv", "no_vehicle");

		$changer = new TripVehicleChanger($this->config);
		$changer->inplace_list_substitute_vehicle("rtw", self::$india_list, "gxv", "no_vehicle");
	}
}
