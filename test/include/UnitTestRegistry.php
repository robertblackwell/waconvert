<?php
use Database\Object as Db;

class UnitTestRegistry
{
	public static $globals = [];
	public static $doc_root;
	public static $package_dir;
	public static $data_root;
	public static $config;
	public static function init()
	{
		self::$package_dir = dirname(dirname(dirname(__FILE__))); // The top level dir of the package
		$config = [
			'sql'=>[
				'db_name'=>"database_package_test_db",
				'db_user'=>"root",
				'db_host'=>"localhost",
				'db_passwd'=>"wara2074",
			],
			'hed'=>[
				'data_root'=>self::$package_dir."/test/data",
				'doc_root'=>self::$package_dir."/test",
				'full_url_root'=>"http:/www.test_whiteacorn/data",
				'url_root'=>"/data",
			]
		];
		self::$data_root = self::$package_dir."/test/data";
		self::$doc_root  = self::$package_dir."/test";
		self::$config = $config;
	}

}
