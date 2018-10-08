<?php
require_once(dirname(__FILE__)."/header.php");


class TestAssetConfig extends UnittestCase{
	function setUp(){
	}
	
	function Test1(){
		$asset = new \WhiteacornControl\Commands\ConfigObject();
		$asset->do_it("dev", "godaddy", "php");
		var_dump($asset->getSourceForOneEntity("item", "dev", "rtw", "slug"));
		var_dump($asset->getSourceForOneEntity("gallery", "dev", "rtw", "slug"));
		var_dump($asset->getSourceForOneEntity("banner", "dev", "rtw", "slug"));
		var_dump($asset->getSourceForOneEntity("nina_rob", "dev", "rtw", "slug"));
		var_dump($asset->getSourceForOneEntity("album", "dev", "rtw", "slug"));
		//var_dump($asset);
		var_dump($asset->getDestinationForOneEntity("item", "dev", "rtw", "slug"));
		var_dump($asset->getDestinationForOneEntity("gallery", "dev", "rtw", "slug"));
		var_dump($asset->getDestinationForOneEntity("banner", "dev", "rtw", "slug"));
		var_dump($asset->getDestinationForOneEntity("nina_rob", "dev", "rtw", "slug"));
		var_dump($asset->getDestinationForOneEntity("album", "dev", "rtw", "slug"));

		var_dump($asset->getSourceForAssetType("php", "dev"));
	}	
}

?>