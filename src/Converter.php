<?php
namespace WaConvert;

use Symfony\Component\Console\Output\OutputInterface;
use WaConvert\Config;
use Database\Locator;
use WaConvert\DataManager;

class Converter
{
	/** @return Converter */
	public function __construct()
	{
	}
	/**
	* @param \SplFileInfo    $dataDirInfo             An SplFileInfo object for the directory to be converted.
	* @param \SplFileInfo    $preconvertedDataDirInfo An SplFileInfo object for the directory of manually
	*                                                 converted itemsto be converted.
	* @param boolean         $nosave                  If true make a copy of the data dir.
	* @param boolean         $dryrun                  If true make no modifications, just show actions expected.
	* @param OutputInterface $output                  Symfony output object to be used by
	*                                                 converter to write console text.
	* @return void
	*/
	public function convert(
		\SplFileInfo $dataDirInfo,
		\SplFileInfo $preconvertedDataDirInfo,
		bool $nosave,
		bool $dryrun,
		OutputInterface $output
	) {
		$config = Config::getInstance();
		$config->dataRoot = $dataDirInfo->getRealPath();
		$config->dataRootSavePath =
			dirname($dataDirInfo->getRealPath()) . "/" . $dataDirInfo->getBasename()."-save";
		$config->doc_root = dirname($dataDirInfo->getRealPath());
		$config->preConvertedDataLocation = $preconvertedDataDirInfo->getRealPath();
		$config->output = $output;
		$config->dryrun = $dryrun;
		$config->nosave = $nosave;

		$locatorConfig = [
			'data_root' => $config->dataRoot,
			'doc_root' => $config->doc_root,
			'full_url_root'=>"http:/www.test_whiteacorn/".$dataDirInfo->getBasename(),
			'url_root'=>"/". $dataDirInfo->getBasename(),
		];

		print_r($locatorConfig);
		Locator::init($locatorConfig);
		
		/** @var $locator \Database\Locator*/
		$locator = \Database\Locator::get_instance();

		$output->writeln("dataRoot: {$config->dataRoot}");
		$output->writeln("dataRootSavePath: {$config->dataRootSavePath}");
		$output->writeln("doc_root : {$config->doc_root}");
		$output->writeln("preconvertedDataDir: {$config->preConvertedDataLocation}");

		$dm = new DataManager($config);
		
		if (!$nosave) {
			$dm->save_data($dryrun);
		}
		(new Home1718($config))->convert();
		(new ERMakeTrip($config))->convert();
		(new Bmw11MakeTrip($config))->convert();
		(new TheAmericasUpdate($config))->convert();
		(new RtwAddVehicle($config))->convert();
		(new IndiaAfrica($config))->convert();
		(new TigerRebuildGallery)->convert();
		$dm->cleanup();
	}
}
