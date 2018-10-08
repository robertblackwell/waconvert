<?php
namespace WaConvert;

use WaConvert\Config;

/**
* This class converts and moves the entries under $locator->trip_root('utah-june-2011')
* into a directory called $locator->trip_root('er'). Which the the correct place
* for a trip called 'er'.
*
* The good news is the utah-june-2011 only have journal entries and a few other bits
* pieces that can be converted manually.
*
* the bad news is that the content files of the utah-june-2011 trip is still in the "old"
* format as used in the earliest version of the whiteacorn site.
* Hence these have to converted to the "new" format.
*
* That conversion requires
*	1.	taking each entry(directory) from inside $locator->trip_root('utah-june-2011')/journals/entries
*		and copying that directory to $locator->trip_root("utah-june-2011")/content. This will get the
*		photos associated with each entry in the right place and create the right folder
*		for the converted content of each journal entry.
*	2.	reformating the content of each
*			$locator->trip_root('utah-june-2011')/content/{yymmdd}/index.php, into a new file called
*			$locator->trip_root('utah-june-2011')/content/{yymmdd}/content.php
*/
class ERMoveUtah
{
	/**
	* @param Config $config Config details for this run of the command.
	* @return void
	*/
	public function __construct(Config $config)
	{
		$this->config = $config;
	}
	/** @return void */
	public function convert()
	{
		$dryrun = $this->config->dryrun;
		$locator = \Database\Locator::get_instance();
		$utah_content_dir = $locator->content_root("utah-june-2011");
		$utah_entries_dir = $locator->entries_root("utah-june-2011");
		// convert old form item into new form and keep in utah-june-2011
		// may need to put the old->new conversion back in here

		// copy entries into content so as to get all files
		if ($dryrun) {
			$this->config->putput->writeln("cp -r {$utah_entries_dir}/ {$utah_content_dir}");
		} else {
			system("cp -r {$utah_entries_dir}/ {$utah_content_dir}");
		}
		print "utah content {$utah_content_dir} utah entries {$utah_entries_dir}\n";

		// now do old form to new form and make the trip value 'utah'
		$converter = new JournalConverter($this->config);
		$converter->convert("utah-june-2011", "utah", $utah_content_dir);
		// now change trip value from 'utah' to 'er' and add vehicle value as 'earthroamer'
		$changer = new TripVehicleChanger($this->config);
		$changer->inplace_change_trip_vehicle("utah-june-2011", "utah", "er", "earthroamer");

		// now copy the modified entries to their final home
		$dest_content_dir = $locator->content_root("er");
		if ($dryrun) {
			$this->config->output->writeln("cp -r {$utah_content_dir}/ {$dest_content_dir}");
		} else {
			system("cp -r {$utah_content_dir}/ {$dest_content_dir}");
		}
	}
	/**
	* The convert method created a
	* @return void
	*/
	public function remove_junk()
	{
		$locator = \Database\Locator::get_instance();
		$xtra = $locator->entries_root("utah-june-2011")."/extras";
		$templates = $locator->entries_root("utah-june-2011")."/Template";

		if (file_exists($xtra)) {
			if ($this->config->dryrun) {
				print "rm -R {$xtra} \n";
			} else {
				system("rm -R {$xtra}");
			}
		}

		if (file_exists($templates)) {
			if ($this->config->dryrun) {
				print "rm -R {$templates}\n";
			} else {
				system("rm -R {$templates}");
			}
		}
	}
	/** @return void */
	public function remove_from_utah()
	{
	}
}
