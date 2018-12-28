<?php 
namespace WaConvert;

class TigerRebuildGallery
{
	/**
	* Constructor
	* @param Config $config The config object for this run of the command.
	* @return ERMakeTrip
	*/
	public function __construct(Config $config)
	{
		$this->config = $config;
		$this->locator = \Database\Locator::get_instance();
		$this->local_data = dirname(dirname(__FILE__))."/data";
		$this->local_er_root = $this->config->preConvertedDataLocationForTrip("er");
	}
	public function convert()
	{
		$gal_path = $this->local_er_root ."/photos/galleries/tiger-rebuild";
		$rtw_galleries = $this->locator->album_root('rtw');
		$theamericas_galleries = $this->locator->album_root('theamericas');
		system("cp -rv {$gal_path}/ {$rtw_galleries}");
		system("cp -rv {$gal_path}/ {$theamericas_galleries}");
	}
}