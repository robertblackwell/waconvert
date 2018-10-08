<?php
namespace WaConvert;

use WaConvert\Config;

class ER16Converter
{
	/**
	*
	*/
	public static $er16_list = [
		"160706",
		"160707",
		"160708",
		"160709",
		"160710",
		"160711",
		"160712",
		"160713",
		"160714",
		"160715",
		"160716",
		"160717",
		"160718",
		"160811",
		"160812",
		"160813",
		"160814",
		"160815",
		"160816",
		"160817",
		"160818",
		"160819",
		"160820",
		"160821",
		"160822",
		"160823",
		"160824",
		"160825",
		"160826",
		"160827",
		"160828",
		"160830",
		"160831",
		"160902",
		"160903",
		"160904",
		"160905",
		"160906",
		"160907",
		"160908",
		"160909",
		"160910",
		"160912",
		"160913",
		"160914",
		"160915",
		"160916",
		"160917",
		"160918",
		"160919",
		"160920",
		"160921",
		"160922",
		"160923",
		"160924",
		"160925",
		"160926",
		"160927",
		"160928",
		"160929",
		"160930",
		"161001",
		"161002",
		"161009",
		"161011",
		"161012",
		"161013",
		"161014",
		"161015",
		"161016",
		"161017",
		"161018",
		"161019",
		"161020",
		"161021",
		"161022",
		"161023",
		"161026",
		"161027",
		"161028",
		"161029",
		"161030",
		"161031",
	];

	public function __construct(Config $config)
	{
		$this->config = $config;
		$this->dryrun = $config->dryrun;
		$this->locator = \Database\Locator::get_instance();
		
		$this->er_content_dir = $this->locator->content_root("er");
		$this->rtw_content_dir = $this->locator->content_root("rtw");
	}
	function convert()
	{
		$dry_run = false;
		$locator = \Database\Locator::get_instance();
		$rtw_content_dir = $locator->content_root("rtw");
		$er_root = $locator->trip_root("er");
		$er_tmp = $er_root . "/tmp_content";
		
		mkdir($er_tmp);

		foreach ($list as $file) {
			$item_src = $locator->item_dir("rtw", $file);
			$item_dest = "{$er_root}/{$file}";
			$
			print "copy item {$file} cp -r {$item_src}/  {$er_tmp}";
		}
		exit();
		// // copy entries into content so as to get all files
		// system("cp -r {$utah_entries_dir}/ {$utah_content_dir}");
		// print "utah content {$utah_content_dir} utah entries {$utah_entries_dir}\n";

		// // now do old form to new form convert
		// $converter = new JournalConverter(false);
		// $converter->convert("utah-june-2011", "utah", $utah_content_dir);

		// // now change trip and add vehicle
		// $changer = new TripVehicleChanger();
		// $changer->inplace_change_trip_vehicle("utah-june-2011", "utah", "er", "earthroamer");

		// // now copy the modified entries to their final home
		// $dest_content_dir = $locator->content_root("er");
		// system("cp -r {$utah_content_dir}/ {$dest_content_dir}");
	}
}
