<?php
namespace WaConvert;

use WaConvert\Config;

class TripVehicleChanger
{
	private $config;
	private $locator;
	/**
	* @param Config $config Config object for run.
	* @return TripVehicleChanger
	*/
	public function __construct(Config $config)
	{
		$this->config = $config;
		$this->locator = \Database\Locator::get_instance();
		// $this->er_content_dir = $this->locator->content_root("er");
		// $this->rtw_content_dir = $this->locator->content_root("rtw");
	}
	/**
	* @return void
	*/
	public function compare()
	{
	}

	/**
	* Change the vehicle value for a list of slugs and replace the existing entry file
	* with the updated version.
	* @param string $trip         A trip code.
	* @param array  $list         A list of slugs.
	* @param string $from_vehicle The current value of vehicle.
	* @param string $to_vehicle   The new value of vehicle.
	* @return void
	*/
	public function inplace_list_substitute_vehicle(string $trip, array $list, string $from_vehicle, string $to_vehicle)
	{
		foreach ($list as $slug) {
			$this->inplace_substitute_vehicle($trip, $slug, $from_vehicle, $to_vehicle);
		}
	}
	/**
	* Add a vehicle value to a list of slugs and in each case replace the current
	* slug file.
	* @param string $trip    A trip code.
	* @param array  $list    A list of slugs.
	* @param string $vehicle The value of vehicle.
	* @return void
	*/
	public function inplace_list_add_vehicle(string $trip, array $list, string $vehicle)
	{
		foreach ($list as $slug) {
			$this->inplace_add_vehicle($trip, $slug, $vehicle);
		}
	}
	/**
	* Replace the value of vehicle in a single entry.
	* @param string $trip         A trip code.
	* @param string $slug         The slug.
	* @param string $from_vehicle The current value of vehicle.
	* @param string $to_vehicle   The new value of vehicle.
	* @return void
	*/
	public function inplace_substitute_vehicle(string $trip, string $slug, string $from_vehicle, string $to_vehicle)
	{
		$locator = \Database\Locator::get_instance();
		$fn = $locator->item_filepath($trip, $slug);
		$content = file_get_contents($fn);
		
		$new_content = str_replace("<div id=\"vehicle\">{$from_vehicle}</div>", "<div id=\"vehicle\">{$to_vehicle}</div>", $content);
		$save_fn = $locator->item_dir($trip, $slug) ."/new3_content.php";
		if (!$this->config->dryrun) {
			file_put_contents($fn, $new_content);
		}
		// file_put_contents($save_fn, $content);
	}
	/**
	* Change the trip value for an entry.
	* @param string $trip      A trip code.
	* @param string $slug      The slug for the entry being changed.
	* @param string $from_trip The current value of trip.
	* @param string $to_trip   The new value of trip.
	* @return void
	*/
	public function inplace_substitute_trip(string $trip, string $slug, string $from_trip, string $to_trip)
	{
		$locator = \Database\Locator::get_instance();
		$fn = $locator->item_filepath($trip, $slug);
		$content = file_get_contents($fn);
		
		$new_content = str_replace("<div id=\"trip\">{$from_trip}</div>", "<div id=\"trip\">{$to_trip}</div>", $content);
		$save_fn = $locator->item_dir($trip, $slug) ."/new3_content.php";

		if (!$this->config->dryrun) {
			file_put_contents($fn, $new_content);
		}
		print __METHOD__."XX {$fn} {$from_trip} --> {$to_trip}\n";
		// file_put_contents($save_fn, $content);
	}
	/**
	* Remove a vehicle div and vehicle value from an entry, match on vehicle string.
	* @param string $trip    A trip code.
	* @param string $slug    A slugs.
	* @param string $vehicle The current value of vehicle.
	* @return void
	*/
	public function inplace_remove_vehicle(string $trip, string $slug, string $vehicle)
	{
		$locator = \Database\Locator::get_instance();
		$fn = $locator->item_filepath($trip, $slug);
		$content = file_get_contents($fn);
		
		$new_content = str_replace("<div id=\"vehicle\">{$vehicle}</div>", "", $content);
		$save_fn = $locator->item_dir($trip, $slug) ."/new2_content.php";
		if (! $this->config->dryrun) {
			file_put_contents($fn, $new_content);
			// file_put_contents($save_fn, $content);
		}
	}
	/**
	* Add a vehicle div to an entry with the given value.
	* @param string $trip    A trip code.
	* @param string $slug    A slug.
	* @param string $vehicle The value of vehicle.
	* @return void
	*/
	public function inplace_add_vehicle(string $trip, string $slug, string $vehicle)
	{
		$locator = \Database\Locator::get_instance();
		$fn = $locator->item_filepath($trip, $slug);
		$content = file_get_contents($fn);

		$new_content = str_replace(">{$trip}</div>", ">{$trip}</div>\n\t<div id=\"vehicle\">{$vehicle}</div>", $content);
		$save_fn = $locator->item_dir($trip, $slug) ."/new_content.php";
		if (! $this->config->dryrun) {
			file_put_contents($fn, $new_content);
			// file_put_contents($save_fn, $content);
		}
	}
	/**
	* For all content entries in the trip $where_trip, change trip value
	* from $from_trip to $to_trip and ADD a vehicle div with the value as $vehicle.
	*
	* @param string $where_trip A trip code that is used to find a folder of trip entries.
	* @param string $from_trip  A current trip code in those entries.
	* @param string $to_trip    A new trip code for those entries.
	* @param string $vehicle    The value of vehicle to be added to the entries.
	* @return void
	*/
	public function inplace_change_trip_vehicle(
		string $where_trip,
		string $from_trip,
		string $to_trip,
		string $vehicle
	) {
		$locator = \Database\Locator::get_instance();
		$where_content_dir = $locator->content_root($where_trip);

		print "Where content dir : {$where_content_dir}\n";
		$folders = array_diff(scandir($where_content_dir), [".", "..", ".DS_Store"]);
		foreach ($folders as $item_dir) {
			$where_content_file = $where_content_dir."/".$item_dir ."/content.php";

			$where_item_dir = $where_content_dir . "/". $item_dir;

			$content = file_get_contents($where_content_file);
			$new_content = str_replace(">{$from_trip}</div>", ">{$to_trip}</div>\n\t<div id=\"vehicle\">{$vehicle}</div>", $content);
			if ($content === $new_content) {
				print "nothing changed \n";
			} else {
				$new_content_file = $where_content_dir."/".$item_dir ."/new_content.php";
				$saved_content_file = $where_content_dir."/".$item_dir ."/saved_content.php";
				print "{$where_content_file} Done remove {$where_item_dir} \n";
				if (! $this->config->dryrun) {
					// file_put_contents($new_content_file, $new_content);
					// file_put_contents($saved_content_file, $content);
					file_put_contents($where_content_file, $new_content);
				}
			}
		}
	}
}
