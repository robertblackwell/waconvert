#!/usr/bin/env php
<?php
// print __METHOD__." dirname 2 of file  ". dirname(dirname(__FILE__)) ."\n";
// print __METHOD__." dirname 3 of file  ". dirname(dirname(dirname(__FILE__))) ."\n";
// print __METHOD__." dirname 4 of file  ". dirname(dirname(dirname(dirname(__FILE__)))) ."\n";

$vendor_dir = dirname(dirname(dirname(dirname(__FILE__))));

$info = new SplFileInfo($vendor_dir);

if ($info->getBasename() !== "vendor") {
	throw new \Exception("require vendor autoload is asking for wrong file {$vendor_dir}");
}

require "{$vendor_dir}/autoload.php";
include dirname(dirname(__FILE__))."/src/Main.php";
?>
