<?php
require_once(dirname(__FILE__)."/header.php");

Trace::disable();

class TestExplicitMethod extends UnittestCase{
	function setUp(){
	    Trace::on(__CLASS__,'Test1');
	}
	function Test1(){
	    Trace::function_entry("the first one");
	    Trace::debug('a debug message');
	    Trace::function_exit("the second one");
	}
	function Test2(){
	    Trace::function_entry("the first one");
	    Trace::debug('a debug message');
	    Trace::function_exit("the second one");
	}
}
class TestExplicitClass extends UnittestCase{
	function setUp(){
	    Trace::on(__CLASS__);
	}
	function Test1(){
	    Trace::function_entry("the first one");
	    Trace::debug('a debug message');
	    Trace::function_exit("the second one");
	}
	function Test2(){
	    Trace::function_entry("the first one");
	    Trace::debug('a debug message');
	    Trace::function_exit("the second one");
	}
}
Trace::on("TestExplicitClassX");
class TestExplicitClassX extends UnittestCase{
	function setUp(){
	}
	function Test1(){
	    Trace::function_entry("the first one");
	    Trace::debug('a debug message');
	    Trace::function_exit("the second one");
	}
	function Test2(){
	    Trace::function_entry("the first one");
	    Trace::debug('a debug message');
	    Trace::function_exit("the second one");
	}
}
Trace::on("TestExplicitMethodX",'Test2');
class TestExplicitMethodX extends UnittestCase{
	function setUp(){
	}
	function Test1(){
	    Trace::function_entry("the first one");
	    Trace::debug('a debug message');
	    Trace::function_exit("the second one");
	}
	function Test2(){
	    Trace::function_entry("the first one");
	    Trace::debug('a debug message');
	    Trace::function_exit("the second one");
	}
}
Trace::on("*",'non_class_function');
function non_class_function($one, $two){
    Trace::function_entry();
    Trace::debug("a debug message one:$one two:$two");
    Trace::function_exit();
}
class TestExplicitFunctionX extends UnittestCase{
	function setUp(){
	}
	function Test2(){
	    Trace::function_entry();
	    non_class_function('111','222');
	    Trace::function_exit();
	}
}
class TestImpliedConstructor extends UnittestCase{
    function __construct(){
        //Trace::reset();
        Trace::off('*', 'non_class_function');
        Trace::on(__CLASS__,__FUNCTION__);
        parent::__construct();
    }
	function setUp(){
	}
	function Test2(){
	    Trace::function_entry();
	    non_class_function('111','222');
	    Trace::function_exit();
	}
}

?>