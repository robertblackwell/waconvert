<?php

class GpxDocument
{
	private $doc;

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
}


(new GpxDocument())
	->loadFile(dirname(__FILE__)."/../combined.gpx") 
	->saveAsJson(dirname(__FILE__)."/../combined.json");


// var_dump($gDoc);
 ?>