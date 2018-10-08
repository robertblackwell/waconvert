<?php
require_once(dirname(__FILE__)."/header.php");

Trace::off();
Trace::on(array("class"=>"test_one", "function"=>"test_1"));

class test_one extends UnittestCase{
	function setUp(){
	}
	public static function st($aparameter){
	    Trace::function_entry();
	    $a="thevariale";
	    Trace::debug("Tried to manipulate the variable a:$a");
	    Trace::function_exit();
	}
	function test_1(){return;
	    Trace::function_entry();
	    Trace::debug("debug from test_1");
	    Trace::function_exit();
	    self::st("thisis the parameter");
	}
	function test_2(){return;
	    Trace::function_entry();
	    Trace::debug("debug from test_2");
	    Trace::function_exit();
	}
	function test_3(){
	    Trace::enable();
	    //Trace::dump();
	    //Trace::disable();
	    //Trace::dump();
	    //Trace::on();
	    //Trace::dump();
	}
}
?>