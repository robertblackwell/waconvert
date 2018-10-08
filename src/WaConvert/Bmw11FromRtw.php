<?php
namespace WaConvert;

use WaConvert\Config;

//
// make a new trip called bmw11 with vehicle bmw1200_2011 from the segment of the rtw trip
// where I rode a motorcycle to alaska
//
class Bmw11FromRtw
{
	/**
	* @var array $bmw11_list A list of journal entries that are in the "rtw" trip that need to be
	* moved into a new bmw11 trip and removed from the "rtw" trip.
	*/
	public static $bmw11_list = [
		"180618",
		"180623",
		"180624",
		"180625",
		"180626",
		"180627",
		"180628",
		"180629",
		"180630",
		"180701",
		"180702",
		"180703",
		"180704",
		"180705",
		"180706",
		"180707",
		"180708",
		"180709",
		"180710",
		"180711",
		"180712",
		"180713",
		"180715",
		"180716",
		"180717",
		"180718",
		"180719",
		"180720",
		"180721",
		"180722",
		"180723",
		"180724",
		"180725",
		"180726",
		"180727",
	];
	/**
	* @var array $post_list A list of posts that need to be converted from "rtw" to the new bmw11 trip.
	*/
	static public $post_list = [
		"tuk18A",
	];
	/**
	* Constructor
	* @param Config $config Config details for this run of the command.
	* @return void
	*/
	public function __construct(Config $config)
	{
		$this->config = $config;
		$this->dryrun = $config->dryrun;
		$this->locator = \Database\Locator::get_instance();
		$this->preconfig_bmw_data = $config->preConvertedDataLocationForTrip("bmw11");
	}
	/**
	* Create the new trip root folder if necessary.
	* @return void
	*/
	public function create_root()
	{
		$d = $this->locator->trip_root("bmw11");
		if (!file_exists($d)) {
			if ($this->dryrun) {
				$this->config->output->writeln("mkdir($d, 0777, true)");
			} else {
				mkdir($d, 0777, true);
			}
		}
	}
	/**
	* Copy previously hand converted entries to new trip folder.
	* @return void
	*/
	public function copy_preconverted_data()
	{
		$bmw_root = $this->locator->trip_root("bmw11");
		if ($this->dryrun) {
			$this->config->output->writeln("system('cp -r {$this->local_er_root}/* {$bmw_root}')");
		} else {
			system("cp -r {$this->preconfig_bmw_data}/* {$bmw_root}");
		}
	}
	/**
	* Move and convert the selected rtw entries to bmw11.
	* @return void
	*/
	public function convert()
	{

		$dry_run = $this->dryrun;
		$locator = \Database\Locator::get_instance();
		$rtw_content_dir = $locator->content_root("rtw");
		$bmw_root = $locator->trip_root("bmw11");

		// make a temporary trip as a place to house the relevant items while we are
		// modifying and assembling them
		$tmp_root = $locator->trip_root("bmw_tmp");
		$bmw_tmp = $locator->content_root("bmw_tmp");
		print "bmw_tmp {$bmw_tmp}\n";
		if (!file_exists($bmw_tmp)) {
			if ($this->dryrun) {
				$this->config->output->writeln("mkdir($bmw_tmp, 0777, true)");
			} else {
				mkdir($bmw_tmp, 0777, true);
			}
		}

		// copy the selected items from the rtw folder to the temp folder
		foreach (self::$bmw11_list as $file) {
			$item_src = $locator->item_dir("rtw", $file);

			print "copy item {$file} cp -r {$item_src}/  {$bmw_tmp}\n";
			print "Need to remove $item_src \n";
			if ($this->dryrun) {
				$this->config->output->writeln("system('cp -r {$item_src}  {$bmw_tmp}')");
			} else {
				system("cp -r {$item_src}  {$bmw_tmp}");
			}
		}
		// now change trip and add vehicle to those newly copied entries
		// to make them "bmw11" entries.
		$changer = new TripVehicleChanger($this->config);
		if (!$this->dryrun) {
			$changer->inplace_change_trip_vehicle("bmw_tmp", "rtw", "bmw11", "bmw1200_2011");
		}

		// now the post list that only needs its trip changed as post's dont have vehicle
		foreach (self::$post_list as $file) {
			$item_src = $locator->item_dir("rtw", $file);

			print "copy item {$file} cp -r {$item_src}/  {$bmw_tmp}\n";
			print "Need to remove $item_src \n";
			if (!$this->dryrun) {
				system("cp -r {$item_src}  {$bmw_tmp}");
				$changer->inplace_substitute_trip("bmw_tmp", $file, "rtw", "bmw11");
			}
		}


		// // now copy the modified entries to their final home
		$dest_content_dir = $locator->content_root("bmw11");
		
		if (!$this->dryrun) {
			system("cp -r {$bmw_tmp}/ {$dest_content_dir}");
			// now clean up the temp trip
			system("rm -Rf {$tmp_root}");
		} else {
			$this->config->output->writeln("system('cp -r {$bmw_tmp}/ {$dest_content_dir}')");
			$this->config->output->writeln("rm -Rf {$tmp_root}\n");
		}
	}
	/**
	* Remove the selected entries from the "rtw" trip folder/
	* @return void
	*/
	public function remove_from_rtw()
	{
		$locator = \Database\Locator::get_instance();
		$list = array_merge(self::$bmw11_list, self::$post_list);
		foreach ($list as $file) {
			$item_src = $locator->item_dir("rtw", $file);
			if ($this->dryrun) {
				$this->config->output->writeln("rm -R {$item_src}");
			} else {
				system("rm -R {$item_src}");
			}
		}
	}
}
