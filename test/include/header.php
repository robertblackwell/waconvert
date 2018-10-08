<?php
use Database\Object as Db;

error_reporting(-1);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);


$loader = require(dirname(dirname(dirname(__FILE__)))."/vendor/autoload.php");
require_once dirname(__FILE__)."/UnitTestRegistry.php";
\Trace::disable();
UnitTestRegistry::init();
global $config;
$config = UnitTestRegistry::$config;
\WaConvert\Config::$dataRoot = UnitTestRegistry::$data_root;
\WaConvert\Config::$preConvertedDataLocation = UnitTestRegistry::$package_dir."/preconverted_data";
var_dump(\WaConvert\Config::$dataRoot);
var_dump(\WaConvert\Config::$preConvertedDataLocation);
