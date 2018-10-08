<?php
namespace WaConvert;

use WaConvert\Config;

//
// make the ox-11 trip which is not active into part of the amercias trip
// by setting the trip value of all entries to "theamericas" and vehicle to "tiger"
//
// COMPLEXITY - these entries are in the OLD format and that format has to be converted as well.
//
class TheAmericasCopyOx11Entries
{
	/**
	* @param Config $config Details for this run.
	* @return TheAmericasCopyOx11Entries
	*/
	public function __construct(Config $config)
	{
		$this->config = $config;
		$this->dryrun = $config->dryrun;
	}
	/** @return void */
	public function convert()
	{
		$locator = \Database\Locator::get_instance();
		print "This converts an oldstyle journal to the new format\n";
		$journal_source_dir = "";
		$conversion_output_dir = "";
		// lets put the converted file into "ox-11/content"
		$output_dir = $locator->content_root("theamericas");

		$ox11_entries = $locator->entries_root("ox-11");
		$ox11_content = $locator->content_root("ox-11");

		$theamericas_content = $locator->content_root("theamericas");

		if (! file_exists($ox11_content)) {
			mkdir($ox11_content, 0777, true);
		}

		// copy over all the entry directories so as to get the albums and other stuff
		system("cp -r {$ox11_entries}/* {$ox11_content}");
		// system("cp -r {$ox11_entries}/* {$theamericas_content}");

		$converter = new JournalConverter($this->config);
		$converter->convert("ox-11", "theamericas", $ox11_content);
		// now copy to theamericas trip
		system("cp -r {$ox11_content}/* {$theamericas_content}");
		// note that at this point the converted entries do not have a vehicle div
	}
}
