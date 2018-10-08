<?php
namespace WaConvert;

use Symfony\Component\Console\Output\OutputInterface;

class Config
{
	public $preConvertedDataLocation;
	public $dataRoot;
	public $dataRootSavePath;
	public $doc_root;
	public $nosave;
	public $dryrun;
	/** @var OutputInterface $output */
	public $output;
	private static $instance = null;
	/** @return Config */
	public static function getInstance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new Config();
		}
		return self::$instance;
	}
	/**
	* @param string $trip Trip code for which preconverted data location is sought.
	* @return string
	*/
	public function preConvertedDataLocationForTrip(string $trip) : string
	{
		if (strtolower($trip) == "er") {
			$ret = $this->preConvertedDataLocation ."/er";
		} else if (strtolower($trip) == "bmw11") {
			$ret = $this->preConvertedDataLocation ."/bmw11";
		} else {
			throw new \Exception("{$trip} invalid for preconverted data");
		}
		if (!is_dir($ret)) {
			throw new \Exception("preconverted data for {$trip} is missing");
		}
		return $ret;
	}
}
