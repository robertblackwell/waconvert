<?php
use Database\Locator;
use Database\Object as Db;

class Firsttest extends LiteTest\TestCase
{
	public function setUp()
	{
		$config = \UnitTestRegistry::$config;
		Db::init($config);
	}
	public function testSomething()
	{
		$this->assertTrue(true);
		print_r(UnitTestRegistry::$globals);
		$journal_name = "ox11";
		var_dump(Locator::get_instance()->journals_root($journal_name));
	}
	public function testJournalConvert()
	{
		$jc = new \WaConvert\JournalConverter(false);
		$jc->convert("ox-11", "ATRIP", dirname(dirname(__FILE__))."/ox11_out");
	}
}