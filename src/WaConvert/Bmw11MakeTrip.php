<?php
namespace WaConvert;

use WaConvert\Config;

/**
* make a bmw trip by:
* 1.	taking the rtw entries that happened over
* 		june july 2018, moving them into the new trip folder "data/bmw11/content,
*		changing the trip value for these entries from 'rtw' to 'bmw11'
* 		and then removing those entries from the "rtw" trip.
* 2.	copying into the new bmw11 trip folder the pre converted data
*		that was hand converted before running this utility.
*
*/
class Bmw11MakeTrip
{
	/**
	* constructor
	* @param Config $config Details for current run of command.
	*/
	public function __construct(Config $config)
	{
		$this->config = $config;
		$this->dryrun = $config->dryrun;
		$this->locator = \Database\Locator::get_instance();
		$this->preconfig_bmw_data = $config->preConvertedDataLocationForTrip("bmw11");
	}
	/**
	* Create the new bmw11 trip root folder.
	* @return void
	*/
	public function create_root()
	{
		$d = $this->locator->trip_root("bmw11");
		if (!file_exists($d)) {
			mkdir($d, 0777, true);
		} else {
			system("rm -R {$d}/* "); // if it exists clean it out so as not get hangers on
		}
	}
	/**
	* Copy into the new trip folder the entries that have been previously hand converted
	* and are stored ,ocally in this repo.
	* @return void
	*/
	public function copy_preconverted_data()
	{
		$bmw_root = $this->locator->trip_root("bmw11");
		system("cp -r {$this->preconfig_bmw_data}/* {$bmw_root}");
	}
	/**
	* Perform the creation and conversion to make the new bmw11 trip folder and contents.
	* @return void
	*/
	public function convert()
	{
		$this->create_root();
		$this->copy_preconverted_data();

		$obj = new Bmw11FromRtw($this->config);
		$obj->convert();

		$obj->remove_from_rtw();
	}
}
