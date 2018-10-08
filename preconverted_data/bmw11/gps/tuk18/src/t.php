<?php

class GpxDocument
{
	public $doc;

	public function loadFile($fileName)
	{
		$this->doc = simplexml_load_file($fileName);
		return $this;
	}
	private function getBounds()
	{
		$b = $this->doc->metadata->bounds;

		$res = [
			'minlat'=>(float)$b['minlat'],
			'maxlat'=>(float)$b['maxlat'],
			'minlon'=>(float)$b['minlon'],
			'maxlon'=>(float)$b['maxlon'],
		];

		return $res;		
	}
	private function getTrackPoints()
	{

		$pts = [];
		foreach($this->doc->trk->trkseg[0]->trkpt as $tp){
			$pts[] = [(float)$tp['lat'], (float)$tp['lon']];
		}
		$res = ['bounds'=> $this->getBounds(), 'points' => $pts];
		return $res;
	}

	function saveAsJson($fileName)
	{
		file_put_contents($fileName, $this->toJson());
	}
	function toJson()
	{
		return json_encode($this->getTrackPoints());
	}
	function listTrkSegs()
	{
		foreach($this->doc->trk->trkseg as $seg) {
			print "next seg "  . count($seg) ."\n";
			foreach($seg as $pt) {
				print "\t next pt \n";
				$time = $pt->time . "";
				print( "time : " . $time . "\n");
				// print " time: " . $seg->trkpt[0]['time'] . "\n";

			}
		}
	}
}


class Segment {
	public $start_time;
	public $end_time;
	public $points;
}

class Point {
	public $lon;
	public $lat;
	public $ele;
	public $time;
}

function mkSegment($ar)
{
	print "mkSegment enter \n";
	// print_r($ar['trkpt']);
	$points = $ar['trkpt'];
	$last_point = $points[count($points) - 1];
	$start_time = $ar['trkpt'][0]['time'];
	$end_time = $last_point['time'];
	$seg = new Segment();
	$seg->start_time = $start_time;
	$seg->end_time = $end_time;
	$seg->points = [];
	foreach ($points as $p) {
		$np = new Point();
		$np->lon = $p["@attributes"]['lon'];
		$np->lat = $p["@attributes"]['lat'];
		$np->ele = $p["ele"];
		$np->time = $p['time'];
		$seg->points[] = $np;
	}	
	// print "mkSegment exit start: {$start_time} end_time : {$end_time} \n";
	return $seg;
}

function mkTrackObject($fileName)
{
	// $doc = new GpxDocument();
	// $doc->loadFile("/Volumes/GARMIN/Garmin/GPX/Current/Current.gpx");
	// $dj = json_encode($doc->doc, JSON_PRETTY_PRINT);
	// $ar = json_decode($dj, TRUE);
	// $track = new \stdClass();
	// $track->segments = [];
	// $seg = new \stdClass();
	// $seg->start_time = $start_time;
	// $seg->end_time = $end_time;
	// $seg->points = [];
	// foreach($ar['trk']['trkseg'] as $seg) {
	// 	// $startTime = $seg[0][];
	// 	foreach($seg as $point) {
	// 		$lon = 
	// 	}

	// }
	// return $seg;
}
// (new GpxDocument())
// 	->loadFile("/Volumes/GARMIN/Garmin/GPX/Current/Current.gpx") 
// 	->listTrkSegs();


$doc = new GpxDocument();
$doc->loadFile("/Volumes/GARMIN/Garmin/GPX/Current/Current.gpx");
$dj = json_encode($doc->doc, JSON_PRETTY_PRINT);
// print_r($dj);
$ar = json_decode($dj, TRUE);
$segs = [];
foreach($ar['trk']['trkseg'] as $k => $tmp) {
	// print "k: $k \n";
	$s = mkSegment($tmp);
	$segs[] = $s;
}
print_r($segs);

// var_dump($gDoc);
 ?>