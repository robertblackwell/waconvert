<?php
namespace WaConvert;

use WaConvert\Config;

class ERMove2016EntriesFromRTW
{

	/**
	* @var array $er16_list List of current rtw entries that must be moved to, and converted to ER entries.
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
	/**
	* @var array $post_list A list of post entries that need to be moved to 'er" trip.'
	*/
	public static $post_list = [
		"1702-doomed-trip-1",
		"1702-doomed-trip-2",
		"180423-er-for-sale",
	];
	/**
	* @param Config $config Config for this execution of command.
	*
	*/
	public function __construct(Config $config)
	{
		$this->config = $config;
	}
	/**
	* @return void
	*/
	public function copy()
	{
		$dryrun = $this->config->dryrun;
		$locator = \Database\Locator::get_instance();
		$rtw_content_dir = $locator->content_root("rtw");
		$er_root = $locator->trip_root("er");

		// make a temporary trip as a place to house the relevant items while we are
		// modifying them
		$tmp_root = $locator->trip_root("er_tmp");
		$er_tmp = $locator->content_root("er_tmp");
		//$er_tmp = $er_root . "/content";
		
		print "er_tmp {$er_tmp}\n";

		if (!file_exists($er_tmp)) {
			if ($dryrun) {
				$this->config->output->writeln("mkdir({$er_tmp}, 0777, true)");
			} else {
				mkdir($er_tmp, 0777, true);
			}
		}

		$changer = new TripVehicleChanger($this->config);
		// copy all the selected entries into the temporary trip content directory
		foreach (self::$er16_list as $file) {
			$item_src = $locator->item_dir("rtw", $file); // the file basename and slug are the same
			if ($dryrun) {
				print "copy item {$file} cp -r {$item_src}/  {$er_tmp}\n";
			} else {
				system("cp -r {$item_src}  {$er_tmp}");
			}

			// not ready for this change yet
			// $changer->inplace_substitute_trip("er_tmp", $file, "rtw", "er");
			// $changer->inplace_add_vehicle("er_tmp", $file, "er");
		}
		// now change trip from 'rtw' to 'er' and add vehicle value of "earthroamer"
		// the "er_tmp" argument is telling the changer where to find the files to be edited
		$changer->inplace_change_trip_vehicle("er_tmp", "rtw", "er", "earthroamer");

		foreach (self::$post_list as $file) {
			$item_src = $locator->item_dir("rtw", $file);
			if ($dryrun) {
				print "copy item {$file} cp -r {$item_src}/  {$er_tmp}\n";
			} else {
				system("cp -r {$item_src}  {$er_tmp}");
			}
			$changer->inplace_substitute_trip("er_tmp", $file, "rtw", "er");
		}


		// // now copy the modified entries to their final home
		$dest_content_dir = $locator->content_root("er");
		if ($dryrun) {
			$this->config->output->writeln("cp -r {$er_tmp}/ {$dest_content_dir}");
		} else {
			system("cp -r {$er_tmp}/ {$dest_content_dir}");
		}

		// now clean up the temp trip
		if ($dryrun) {
			print("rm -Rf {$tmp_root}\n");
		} else {
			system("rm -Rf {$tmp_root}");
		}
	}
	/**
	* @return void
	*/
	public function remove_from_rtw()
	{
		$locator = \Database\Locator::get_instance();
		$list = array_merge(self::$er16_list, self::$post_list);
		foreach ($list as $file) {
			$item_src = $locator->item_dir("rtw", $file);

			print "rm -R {$item_src}\n";
			system("rm -R {$item_src} ");
		}
	}
}
