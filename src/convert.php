<?php

$loader = require(dirname(dirname(__FILE__))."/vendor/autoload.php");


if(count($argv) != 2) {
	print "wrong number of arguments";
	exit();
}
if($argv[1] == "all")
{
	// Registry::$globals->doc_root = "";
	
	$dm = new \WaConvert\DataManager(dirname(__FILE__));
	\WaConvert\Config::$dataRoot = dirname(dirname(__FILE__))."/test/data";
	\WaConvert\Config::$preConvertedData = dirname(dirname(__FILE__))."/preconverted_data";
	var_dump(\WaConvert\Config::$dataRoot);
	var_dump(\WaConvert\Config::$preConvertedDataLocation);
	exit();
	/**
	 * @var $locator \Database\Locator
	 */
	$locator = \Database\Locator::get_instance();
	$h1718 = $locator->item_dir("rtw", "home17-18");
	system("rm -R {$h1718}");

	(new ERMakeTrip())->convert();
	(new Bmw11MakeTrip())->convert();
	(new TheAmericasUpdate())->convert();
	(new RtwAddVehicle())->convert();
	(new IndiaAfrica())->convert();
	$dm->cleanup();
}

// test_ox_11_conversion();

?>
