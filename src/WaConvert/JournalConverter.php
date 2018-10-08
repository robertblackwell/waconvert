<?php
namespace WaConvert;

use WaConvert\Config;
use \Journal as Journal;
use \VOFactory as VOFactory;

class JournalConverter
{
	/**
	* @param Config $config Details for this run.
	* @return TheAmericasJournalConverter
	*/
	public function __construct(Config $config)
	{
		$this->config = $config;
		$this->dryrun = $config->dryrun;
		print "constructor has run\n";
	}
	/** @return void */
	public function setUp()
	{
		// Database::setConfig(array("db_host"=>"localhost", "db_name"=>"wa", "db_user"=>"root", "db_passwd"=>""));
		// $db = Database::getInstance();
	}
	/**
	* This converts an old style Journal into a new style journal and writes the new style to an output dirctory.
	*
	* @param string $journal_name The name of the source journal in old style format.
	* @param string $to_trip      If provided the conversion should change the $trip value of
	*                             the entries in the old style journal. If not provided keep the
	*                             trip name the same as the journal_name.
	* @param string $output_dir   Optionally the path to the output directory. If not provided
	*							  is deduced fom the trip name.
	*
	* @return void
	*/
	public function convert(string $journal_name, string $to_trip = null, string $output_dir = null)
	{
		$trip = $journal_name;

		print "test_convert journal_name : {$journal_name} output_dir : {$output_dir}\n";
		print "Convert Old Format Journal Entries To New Format and Create Items Directories\n";
		$lazy = false;
		$j = new Journal();
		$j->loadFromFullPath(\Database\Locator::get_instance()->journals_root($journal_name), $journal_name, $lazy);


		$locator = \Database\Locator::get_instance();
		/**
		* This is the directory into which we put the new format item directories
		* we might have to create this directory. To allow repeatablility of the conversion
		* we should delete it and recreate it
		*/
		$content_root = $locator->content_root($trip); // journal name and trip name are the same thing

		if (is_null($output_dir))
			$output_dir = $content_root;
		else {
		}

		if (file_exists($content_root) && is_dir($content_root)) {
		} else {
		}
		/**
		* This is the directory that contains the old format item directories
		*/
		$entries_root = $locator->entries_root($journal_name);
		
		print "Journal lives here : {$j->journalRoot}\n";
		print "Content root is : {$content_root}\n";
		print "Journal Entries root is : {$entries_root} \n";
		print "Required preliminary MANUAL step \n";
		print "\tcp -Rvf {$entries_root} {$content_root} \n";
		print "\tand then convert {$entries_root}/slug/original.php --> {$output_dir}/slug/content.php \n";

		$dryrun = $this->dryrun;

		$ents = $j->getEntriesList();

		$list=array();

		foreach ($ents as $e) {
			// print "Converting Journal Entry trip: {$e->myTripName} Entry Slug:  {$e->myEntryName}\n";
			$s = VOFactory::string_from_oldstyle_Entry($e, $to_trip);
			// print $s . "\n";
			// $z = VOFactory::from_oldstyle_entry($e);
			$slug = $e->myEntryName;

			$item_dir = "{$output_dir}/{$slug}";
			$fn = "{$item_dir}/content.php";
			if (! $dryrun) {
				if (! file_exists($item_dir)) {
					if (!$this->dry_run)
					mkdir($item_dir, true);
					chmod($item_dir, 0777);
				}
				file_put_contents("{$item_dir}/content.php", $s);

				$original_html = "{$item_dir}/original.html";
				if (file_exists($original_html)) {
					system("rm {$original_html}");
				}
				$index_html = "{$item_dir}/index.html";
				if (file_exists($index_html)) {
					system("rm {$index_html}");
				}

				$stamp = "{$item_dir}/stamp";
				if (file_exists($stamp)) {
					system("rm {$stamp}");
				}

				print "\tConverted {$slug} -> {$fn}   item_dir : {$item_dir}\n";
			} else {
				print "\tDRY RUN Converted {$slug} -> {$fn}   item_dir : {$item_dir}\n";
			}
		}
		//var_dump($list);
		print "Conversion Complete\n";
	}
}
