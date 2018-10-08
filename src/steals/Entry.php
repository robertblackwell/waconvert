<?php
$debug = false;
/*!
*/
define( 'ENTRY_VERBOSITY_SILENT', 1);
define( 'ENTRY_VERBOSITY_REPORT' , 2);
define( 'ENTRY_VERBOSITY_DEBUG_LEVEL1', 3);
define( 'ENTRY_VERBOSITY_DEBUG_LEVEL2' , 4);
/*! 
* @ingroup   Journal
* 	A class that represents a single entry in a Journal. This class provides access
*  	to the data elements in a journal entry in both text and DOMNode format. It is a
*  	MODEL in a MVC framework.
*
* @todo remove all the URL related methods and replace with calls to @link Link_to @endlink
* @todo move all Country related code into a new <strong>Country</strong> class or into @link Model @endlink
*/
class Entry
{
    /*!
    * A DOMDocument containing the journal entry in its original authored HTML form.
    * The file version is never modified. The in memory version is standardized
    * as over time there have been two slightly different formats.
    */
	public  $myDocument; 	

	/*!
     * string - the name of the entry. 
	 * Note all entries conform to a naming standard of the form
     * YYMMDD, so that 091213 is the 13 December 2009.
     */
    public  $myEntryName;       /*! the entries name such as "091223" */
    public  $myJournalName;     /*! the name of the journal to which I belong*/
    public  $myTripName;        /*! the name of the trip to which I belong */
	private $_myMiles;			/*!type string*/
	private $_myLat;   			/*!Latitude as type GPSlocations*/
	private $_myLng;			/*!Longitude as type GPSLocations*/
    private $_body;
    private $_camping;
    
	private $_myJournal;	    /*! A reference to the Journal to which this Entry belongs */      
	
	private $_locator;
	
	/*!
	* holds a value which represents this entries position (that is index) in the parent Journal's list.
	 **/
	private $_positionInJournal;

	public function printDocument($label)
	{
		print "<h3>$label....Start Dump of entry document entrywrapper*************************</h3>";
		//$s = elementHTML($this->myDocument->getElementById("EntryWrapper"));
		$s  = $this->myDocument->saveHTML();
		print $s;
		print "<h3>$label ...End Dump of entry document entrywrapper  *************************</h3>";
	}
	/*
	** The only PUBLIC interface for laoding a journal entry
	** @parms Journal	the journal for which the entry is to be loaded
	** @parms $name		the name of the entry to be loaded
	*/
	public function loadEntry(Journal $journal, $entry_name)
	{
		//print "<p>Entry::load_new ". get_class($journal) .":" .$entry_name."</p>";
		$this->myEntryName = $entry_name;
		$this->_myJournal = $journal;
        $this->myTripName = $journal->myTrip();
        $this->myJournalName = $journal->myName();
		//print "<h1>Got here</h1>";
        $original_path = $this->_myJournal->entriesDir."/".$entry_name."/original.html";
        $s = file_get_contents($original_path);
	    $this->load_entry_from_string($s, $journal->myTrip(), $journal->myName(), $entry_name);
	}
	private function load_entry_from_string($string, $trip, $journal_name, $entry_name, $locator=null)
	{
		$debug = false;
		$report = false;
		if ($debug) print "Entry::load_entry_from_string  ($trip $journal_name $entry_name)";
		$tmp = new DOMDocument();
		$this->myDocument = $tmp;
		$this->rawString = $string;
        $tmp->loadHTML($string);
		$this->myEntryName = $entry_name;
        $this->myTripName = $trip;
        $this->myJournalName = $journal_name;
		/*
		 * set the location data into class variables
		*/
		$el = $this->myDocument->getElementById("EntryMLL");
		if ($el)
		{
			if ($debug) print " Short form ";
			$l = $el->textContent;
			$a = preg_split("[\t]", $l);
			$this->_myMiles = "Miles: " . $a[0];
			$this->_myLat = GPSCoordinate::createLatitude($a[1]);
			$this->_myLng = GPSCoordinate::createLongitude($a[2]);
			$this->removeShortFormLocation();
			if ($debug) print " Location" . $l ."<br>";
		}
		else
		{
			if ($debug) print "<br> long form ";
			if ($debug) print " " . $this->myDocument->getElementById("EntryMiles")->textContent . " " ;
			if ($debug) print " " . $this->myDocument->getElementById("EntryLat")->textContent ." ";
			if ($debug) print " " . $this->myDocument->getElementById("EntryLng")->textContent ."-<br>";
		
			$this->_myMiles = $this->myDocument->getElementById("EntryMiles")->textContent;

			$this->_myLat = GPSCoordinate::createLatitude ($this->myDocument->getElementById("EntryLat")->textContent);
			$this->_myLng = GPSCoordinate::createLongitude($this->myDocument->getElementById("EntryLng")->textContent);

			if ($debug) print " Lat ". $this->_myLat->toString()  ." ";
			if ($debug) print " Lng ". $this->_myLng->toString()  ."<br>";
			
			$this->removeLongFormLocation();
 		}
		$this->setLongLocationMiles($this->_myMiles);
		$this->setLongLocationLat($this->_myLat);
		$this->setLongLocationLng($this->_myLng);
		$this->myDocument->loadHTML($this->myDocument->saveHTML());//hack to force reload
	}
    /*!
     * remove from $this->myDocument any short form version of location data.
     *
     * The standardized for of the raw page has location data in long form.
	 * removing short form location data is a step towards standardizing the page.
     * The existence of such data will be signaled by the existence of a DOMNode
     * with an id=="EntryMLL". If present this element will contain the short form
     * location data and hence deleting this element will achieve the goal.
     */
	private function removeShortFormLocation()
	{
		$sloc = $this->myDocument->getElementById("EntryMLL");
		if ($sloc)
		{
			$p = $sloc->parentNode;
			$osloc = $p->removeChild($sloc);	
		}	
	}
	private function removeLongFormLocation()
	{
		$sloc = $this->myDocument->getElementById("EntryMiles");
		if ($sloc)
		{
			$p = $sloc->parentNode;
			$osloc = $p->removeChild($sloc);	
		}	
		$sloc = $this->myDocument->getElementById("EntryLat");
		if ($sloc)
		{
			$p = $sloc->parentNode;
			$osloc = $p->removeChild($sloc);	
		}	
		$sloc = $this->myDocument->getElementById("EntryLng");
		if ($sloc)
		{
			$p = $sloc->parentNode;
			$osloc = $p->removeChild($sloc);	
		}	
	}
	/*!
	* A utility function that overcomes the fact that in some journal entries the country
	* name field is provided in an abbreviated form. This function expands the abbreviation
	* if appropriate otherwise returns the original country code as the full name.
	* @param String $code - the abbreviated country name
	* @return String the full non abbreviated name
	*/
	public function countryCodeLookUp($code){
		$countryCodes = Array(
			"Co"=>"Colombia",
			"Col"=>"Colombia",
			"Colo"=>"Colorado",
			"ES"=>"El Salvador",
			"Nic"=>"Nicaragua",
			"TX"=>"Texas",
			"AZ"=>"Arizona",
			"SC"=>"South Carolina",
			"NC"=>"North Carolina",
			"ID"=>"Idaho",
			"OR"=>"Oregon",
			"Oregon"=>"Oregon",
			"MT"=>"Montana",
			"WA"=>"Washington",
			"KY"=>"Kentucky",
			"KS"=>"Kansas",
			"YT"=>"Yukon",
			"YK"=>"Yukon",
			"UT"=>"Utah",
			"IL"=>"Illinois",
			"Il"=>"Illinois",
			"KT"=>"Kentucky",
			"NWT"=>"North West Territory",
			"NT"=>"North West Territory",
			"AB"=>"Alberta",
			"AK"=>"Alaska",
			"BC"=>"British Columbia",
			"MX"=>"Mexico",
			"ES"=>"El Salvador",
			"Nic"=>"Nicaragua",
			"PAN"=>"Panama",
		);
		if (isset($countryCodes[$code]))
			$x = $countryCodes[$code];
		else
			$x = $code;
		//print "countryCodeLookUp $code r: $r<br>";
		return $x;
	}
	/*!
	* Accessor method - returns the second part of the location field. The piece after
	* the ",". This is usually the country except in the US and Canada where it is the name
	* of the state or province.
	* @return String
	*/
	public function getCountry()
	{
		$el = $this->myDocument->getElementById("EntryTitle");
		$a = preg_split("[,]", $el->textContent);
		$s = $a[1];
		$s = trim($s);	
		$k = $this->countryCodeLookUp($s);
		//$c = $this->countryLookUp($k);
		if ($k == null){// || ($c == null)){				
			var_dump($a);
			var_dump($el);
			$sss = $el->textContent;
			print "el.text: $sss   s: $s  k: $k<br>";
			return "ERROR+++++++".$s;
		}
		return $k;			
	}
	public function getMyPathName()
	{
		return $this->_myJournal->nameServer->entryFullPathName($this->myEntryName);
	}
	public function toString()
	{
		$el = $this->myDocument->getElementById("EntryWrapper");
		$s = ExtendedDOMNode::create($el)->innerHTML();
		return $s;
		return "lat: " . $this->_myLat->toString() . " Lng: " . $this->_myLng->toString();
	}
	//
	// accessor method for some of the data fields in an journal entry - returning text strings
	//
	public function getEntryDayNumber()
	{
		$el = $this->myDocument->getElementById("EntryDayNumber");
		return $el->textContent;
	}
	public function getEntryDate()
	{
		$el = $this->myDocument->getElementById("EntryDate");
		return $el->textContent;
	}
	/*!
	* get the Abstract text from a journal entry. This function has been difficult to get
	* right. There seems to be non printing characters in the text.
	* @return String 
	*/
	public function getEntryAbstract()
	{
		$el = $this->myDocument->getElementById("EntryAbstract"); 
		$s = $el->textContent; 
		//$s = ExtendedString::create(ExtendedDOMNode::create($el)->innerHTML())->removeNulls();
		$s = str_replace("<p>","",str_replace("</p>","",$s));
		return $s;
	}

	/*!
	* get the Abstract html text from a journal entry. This function has been difficult to get
	* right. There seems to be non printing characters in the text.
	* @return String inner HTML of the Abstract element
	*/
	public function getEntryAbstractHTML()
	{
		$el = $this->myDocument->getElementById("EntryAbstract"); 
		$s = ExtendedString::create(ExtendedDOMNode::create($el)->innerHTML())->removeNulls();
		return $s;
	}
	public function getEntryTitle()
	{
		$el = $this->myDocument->getElementById("EntryTitle");
		return ExtendedString::create(ExtendedDOMNode::create($el)->innerHTML())->removeNulls();
	}
	public function getEntryPlace()
	{
		$el = $this->myDocument->getElementById("EntryTitle");
		$a = preg_split("[,]", $el->textContent);
		$s = $a[0];
		$s = trim($s);	
		return $s;
	}
	public function getEntryLocationName()
	{
		return $this->getEntryPlace() . ", " . $this->getCountry();
	}
	/*!
	* Get the inner HTML text of the body of the journal entry
	* @return String 
	*/
	public function getEntryBody()
	{
		$el = $this->myDocument->getElementById("EntryBody");
		return ExtendedDOMNode::create($el)->innerHTML();
		//return $el->textContent;
	}
	public function getEntryText()
	{
		$el = $this->myDocument->getElementById("EntryText");
		return ExtendedDOMNode::create($el)->innerHTML();
		//return $el->textContent;
	}
	/*!
	* Get the innerHTML text of the miles element of a journal entry
	* @return String
	*/
	public function getEntryMiles()
	{
		return $this->_myMiles;
	}
	/*!
	* Get the Lat for a journal entry formated as DD MM.MMM with N/S E/W prefix and degree symbols
	* @return String
	*/
	public function getFormatedEntryLat()
	{
		return $this->_myLat->getFormatedCoordinateDDMM_MMM();
	}
	/*!
	* Get the Lng for a journal entry formated as DD MM.MMM with N/S E/W prefix and degree symbols
	* @return String
	*/
	public function getFormatedEntryLng()
	{
		return $this->_myLng->getFormatedCoordinateDDMM_MMM();
	}
	public function getEntryLat()
	{
		//print "<p> ToString: ". $this->_myLat->toString()  ."</p>";
		return $this->_myLat->getFormatedCoordinateDD_DDD();
		return $this->_myLat->getCoordinateDDMM_MMM();
	}
	public function getEntryLng()
	{
		//print "<p> ToString: ". $this->_myLng->toString()  ."</p>";
		return $this->_myLng->getFormatedCoordinateDD_DDD();
		return $this->_myLng->getCoordinateDDMM_MMM();
	}
	public function getEntryLatDD_DDD()
	{
		//print "<p> ToString: ". $this->_myLat->toString()  ."</p>";
		return $this->_myLat->getCoordinateDD_DDD();
	}
	public function getEntryLngDD_DDD()
	{
		//print "<p> ToString: ". $this->_myLng->toString()  ."</p>";
		return $this->_myLng->getCoordinateDD_DDD();
	}
	public function getMyLat(){
		return $this->_myLat;
	}
	public function getMyLng(){
		return $this->_myLng;
	}
	public function getEntryCamping()
	{
		$el = $this->myDocument->getElementById("Camping");
		if ($el)
    		return ExtendedDOMNode::create($el)->innerHTML();
    	return null;
		return $el->textContent;
	}
	private function setLongLocationLat($lat)
	{
		$debug = false;
		if ($debug) print "<p>Entered setLongLocationLat location is: " . $lat->toString() . "</p>";
		$locationDiv = $this->myDocument->getElementById("EntryLocationDiv");
		$latitudeElement = $this->myDocument->createElement("div", $lat->getFormatedCoordinateDDMM_MMM());
		$latitudeElement->setAttribute("id", "EntryLat");
		$latitudeElement->setAttribute("class","EntryLocationLine" );
		$latitudeElement = $locationDiv->appendChild($latitudeElement);
		$latitudeElement->setIdAttribute("id", true);   //why does not it know this
		if ($debug) print "<p>exit setLongLocationLat</p>";
	}
	private function setLongLocationLng($lng)
	{
		$debug = false;
		if ($debug) print "<p>Entered setLongLocationLat location is: " . $lng->toString() . "</p>";
		$locationDiv = $this->myDocument->getElementById("EntryLocationDiv");
		$lngElement = $this->myDocument->createElement("div",$lng->getFormatedCoordinateDDMM_MMM());
		$lngElement->setAttribute("id", "EntryLng");
		$lngElement->setAttribute("class","EntryLocationLine" );
		$lngElement = $locationDiv->appendChild($lngElement);
		$lngElement->setIdAttribute("id", true);   //why does not it know this
		if ($debug) print "<p>exit setLongLocationLat</p>";
	}
	private function setLongLocationMiles($m)
	{
		$debug = false;
		if ($debug) print "<p>Entered setLongLocationMiles m is: " . $m . "</p>";
		$locationDiv = $this->myDocument->getElementById("EntryLocationDiv");
		$milesElement = $this->myDocument->createElement("div");
		$milesElement->setAttribute("id", "EntryMiles");
		$milesElement->setAttribute("class","EntryLocationLine" );
		$mText = $this->myDocument->createTextNode($m);
		$mText = $milesElement->appendChild($mText);
		$milesElement = $locationDiv->appendChild($milesElement);
		$milesElement->setIdAttribute("id", true);   //why does not it know this

		if ($debug) print "<p>exit setLongLocationMiles</p>";
	}
	public function get_my_journal(){
		return $this->_myJournal;
	}
	public function xtoString()
	{
		return "lat: " . $this->_myLat->toString() . " Lng: " . $this->_myLng->toString();
	}
	public function setPositionInJournal($p)
	{
		$this->_positionInJournal = $p;
	}
	public function getPositionInJournal()
	{
		return $this->_positionInJournal;
	}
	public function getEntryShortDescription()
	{
        return DateTime::createFromFormat("ymd", $this->myEntryName)->format("jS F Y");
	}
	public function getRawHtml(){
	    return $this->rawString;
	}
	public function getContent(){
	    $w = $this->myDocument->getElementById("EntryWrapper");
	    return $this->myDocument->saveHTML($w);
	}
	
}


 ?>