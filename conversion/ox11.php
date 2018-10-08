
<?php
use GetOpt\GetOpt;
use GetOpt\Option;
use GetOpt\Command;
use GetOpt\ArgumentException;
use GetOpt\ArgumentException\Missing;


$unit_test_dir = realpath("../");
$tests_dir = dirname($unit_test_dir);
// print "<p>unit_tests directory: $unit_test_dir</p>\n";
// print "<p>tests_tests directory: $tests_dir</p>\n";
require_once $tests_dir."/include/bootstrap.php";
require_once dirname(__FILE__)."/VOFactory.php";
require_once dirname(__FILE__)."/copy.php";
require_once dirname(__FILE__)."/move.php";



//error_reporting(0);
//require_once('/usr/local/simpletest/autorun.php');
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
function millitime() {
  $microtime = microtime();
  $comps = explode(' ', $microtime);

  // Note: Using a string here to prevent loss of precision
  // in case of "overflow" (PHP converts it to a double)
  //$f = (float)$comp[0] * 1000.00;
  
  return sprintf('%d%03d', $comps[1], $comps[0] * 1000);
}
class JournalConverter// extends UnitTestCase
{
	function __construct()
	{
		print "constructor has run\n";
	}
	function setUp()
	{
		Database::setConfig(array("db_host"=>"localhost", "db_name"=>"wa", "db_user"=>"root", "db_passwd"=>""));
		$db = Database::getInstance();
	}
	/**
	* This converts an old style Journal into a new style journal and writes the new style to an output dirctory.
	*
	* @param $journal_name string - the name of the source jounal in old style format
	* @param $to_trip string - if provided the conversion should change the $trip value of
	*							the entries in the old style journal. If not provided keep the
	*							trip name the same as the journal_name
	* @param $output_dir string - optionally the path to the output directory. If not provided
	*								is deduced fom the trip name
	*
	* @return nothing
	*/
	function convert($journal_name, $to_trip = null, $output_dir = null)
	{
		$trip = $journal_name;

		print "test_convert journal_name : {$journal_name} output_dir : {$output_dir}\n";
		$dr = Registry::$globals->doc_root;
		// $content_dir = $dr."/data/ox-11/content";
		print "Convert Old Format Journal Entries To New Format and Create Items Directories\n";
		$j = Model::get_journal($journal_name);
		$locator = \Database\Locator::get_instance();
		/**
		* This is the directory into which we put the new format item directories
		* we might have to create this directory. To allow repeatablility of the conversion
		* we should delete it and recreate it
		*/
		$content_root = $locator->content_root($trip); // journal name and trip name are the same thing

		if(is_null($output_dir))
			$output_dir = $content_root;
		else {

		}

		if( file_exists($content_root) && is_dir($content_root)) {

		} else {

		}
		/**
		* This is the directory that contains the old format item directories
		*/
		$entries_root = $locator->entries_root($journal_name);
		
		print "Journal lives here : {$j->journalRoot}\n";
		print "Content root is : {$content_root}\n";
		print "Journal Entries root is : {$entries_root} \n";
		print "Rqeuired preliminary MANUAL step \n";
		print "\tcp -Rvf {$entries_root} {$content_root} \n";
		print "and then convert {$entries_root}/slug/original.php --> {$output_dir}/slug/content.php \n";

		$dry_run = true;

		$ents = $j->getEntriesList();

		$list=array();

		foreach($ents as $e){
			// print "Converting Journal Entry trip: {$e->myTripName} Entry Slug:  {$e->myEntryName}\n";
			$s = VOFactory::string_from_oldstyle_Entry($e, $to_trip);
			// print $s . "\n";
			// $z = VOFactory::from_oldstyle_entry($e);
			$slug = $e->myEntryName;
			// $z->trip="rtw";
			$item_dir = "{$output_dir}/{$slug}";
			$fn = "{$item_dir}/content.php";
			if (! $dry_run) {

				if(! file_exists($item_dir))
				{
					mkdir($item_dir, true);
					chmod($item_dir, 0777);
				}
				file_put_contents("{$item_dir}/content.php", $s);
			}
			print "\tConverted {$slug} -> {$fn}\n";
		}
		//var_dump($list);
		print "Conversion Complete\n";
	}

}
/**
* This script provides the following functions
* 1.	Convert ox-11 trip to part of theamericas trip and store the new entries locally to this test directory
*
* 2.	Convert all utah-june-2011 entries to a new trip called "er" plus amend skins appropriately for this to
*
* 3. 	Move those "rtw" entries from 2016 that cover 6/6/16 - 9/31/16  into the "er" trip
*
* 4.	Add a <div id="vehicle">xxxxxx</div> to ALL content.php files for all trips
*
*/
function convert_ox11()
{
	print "This converts an oldstyle jounal to the new format\n";
	$journal_source_dir = "";
	$conversion_output_dir = "";
	$output_dir = realpath("./")."/ox-11";
	// system("rm -R {$output_dir}/*");
	$converter = new JournalConverter();
	$converter->convert("ox-11", "theamericas", $output_dir);
}
function test_ox_11_conversion()
{
	$j =  Model::get_journal("theamericas");
	print_r($j);
}

class Ox11Command extends Command
{
    public function __construct()
    {
        parent::__construct('ox11', [$this, 'handle']);
        
        $this->addOperands([
            // Operand::create('file', Operand::REQUIRED)
            //     ->setValidation('is_readable'),
            // Operand::create('destination', Operand::REQUIRED)
            //     ->setValidation('is_writable')
        ]);
        
    }
    
    public function handle(GetOpt $getOpt)
    {
    	test_ox_11_conversion();
    } 
}