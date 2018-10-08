<?php
namespace WaConvert;

use WaConvert\Config;

/**
* Make a new trip called "er" in $locator->trip_root('er') - which is the expected place.
* And populate that trip with data as follows:
*
*	1.	Make the trip root directory.
*	2.	Copy in the manually converted data.
*	3.	Convert and copy in the utah-june-2011 entries. (see ERMoveUtah.php for details)
*	4.	Copy and convert the 'rtw' entries for 2016 that were really 'er' activities, and
*		remove those entries from the 'rtw' trip.
*		For details see ERMove2016EntriesFromRtw.php
*
*/
class ERMakeTrip
{
	/**
	* Constructor
	* @param Config $config The config object for this run of the command.
	* @return ERMakeTrip
	*/
	public function __construct(Config $config)
	{
		$this->config = $config;
		$this->locator = \Database\Locator::get_instance();
		$this->local_data = dirname(dirname(__FILE__))."/data";
		$this->local_er_root = $this->config->preConvertedDataLocationForTrip("er");
	}
	/**
	* Crate an empty root folder for the er trip.
	* @return void
	*/
	public function create_root()
	{
		$d = $this->locator->trip_root("er");
		if (!file_exists($d)) {
			mkdir($d, 0777, true);
		} else {
			// clean out the directory
			if ($this->config->dryrun) {
				$this->config->output->writeln("rm -R {$d}/* ");
			} else {
				system("rm -R {$d}/* "); // if it exists clean it out so as not get hangers on
			}
		}
	}
	/**
	* @return void
	*/
	public function copy_preconverted_data()
	{
		$er_root = $this->locator->trip_root("er");
		if ($this->config->dryrun) {
			$this->config->output->writeln("cp -r {$this->local_er_root}/* {$er_root}");
		} else {
			system("cp -r {$this->local_er_root}/* {$er_root}");
		}
	}
	/**
	*
	* @return void
	*/
	public function convert()
	{
		$this->create_root();
		$this->copy_preconverted_data();
		// get and converted entries from utah-june-2011. These do not need to be removed
		// as they were already in a place not used in live system
		$utah = new ERMoveUtah($this->config);
		$utah->remove_junk();
		$utah->convert();

		// get and convert entries for travels in ER in 2016, then remmove from rtw.
		$rtw2016 = new ERMove2016EntriesFromRTW($this->config);
		$rtw2016->copy();
		$rtw2016->remove_from_rtw();
	}
}
