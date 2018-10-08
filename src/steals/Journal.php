<?php
/*!
* @defgroup Journal
* Groups together all the classes that are used for daily journals.
*  @ingroup Journal
* 
*      	The components of a Journal stored on disk in a permanent form are
*      	contained in a directory. 
*		@par Identifying Journals
*		They are organized as named entities within a trip. So to specify a Journal
*		you must give a value for $trip and a value for $journal_name.
*		@par For example
*      	For example if the site is www.whiteacorn.com and the doc root is:
*	   	@code
*      	blah/..../blah/whiteacorn then a journal in a directory such as </li>
*      	blah/..../blah/whiteacorn/data/amazon_trip/myjournals</li>
*      	
*		would have a $trip name of amazon_trip and the journal_name would be  myjournals</li>
*      	
*		@endcode
*		@remark
*		The above is a change from previous versions where the site relative path name
*		was the same as the journal name
*
*      	@remark
*		no leading or trailing '/' characters.</p>
*
*      	The convention adopted is that directory names will NOT have the trailing '/'
*
*      	The directory/file structure of a journal entry is
*
* @code
*      journaldir
*
*      ---->indexPage.php          @deprecated indexPage - the page for the journal url listing all entries in summary form
*
*      ---->entries                a directory containing all the entries for this journal
*          ...
*          ...
*          --->091211              a directory containing the entry for 11 December 2009
*              --->original.html   the original entry content as written by the author
*              --->index.php       @deprecated index.php - a formated version of the entry as make by this package
*              --->Images          Large size images used in that entry
*              --->Thumbnails
*          ...
*          ...
* @endcode
* @deprecated indexPage.php and the journal entry file index.php are no longer used. The pages for these things are full dynamic
*/
/*!
*/
define( 'JOURNAL_VERBOSITY_SILENT', 1);
define( 'JOURNAL_VERBOSITY_REPORT' , 2);
define( 'JOURNAL_VERBOSITY_DEBUG_LEVEL1', 3);
define( 'JOURNAL_VERBOSITY_DEBUG_LEVEL2' , 4);
$debug = false;


/*! @ingroup Journal
 *	Represents a full journal and all its entries. This class is a collection object
 *  in that is contains all the entries for a journal and provides access to those. It also 
 *  provides service methods which hide the details of file structure and naming conventions. 
 *  It does this through the use of a name server object which it then mimics.
 * @todo how to remove the dependency on WHITEACORN_DOC_ROOT
 */
class Journal
{

	private $_trip;			/*! the trip this journal belongs to */
	private $_name; 		/*! the name of the journal*/
	/*!
	 * This Journal and all Journal_Entry object in this journal use this name server to resolve file/URL names
	 * @var JournalNameServer $nameServer
	*/
	public $nameServer;
	public $locator;
	/*!
	* Html string containing introduction blurb for the journals index page
	*/
	public $_intro_html;	
	/*!
	 * The full path name for this  Journal's directory
	 * @var string $journalRoot
	*/
	
	public	$journalRoot;
	/*!
	 * The full path name for the directory that holds the entries for this journal
	 * @var string $entriesDir
	*/
	public $entriesDir;
	/*!
	 * An array that holds an Entry object for each entry in this journal
	 * @var array $_entriesList
	*/
	private	$_entriesList;
	/*!
	 * An associative array that holds all the journal entries and is index by the entry name
	 * @var array $_entriesByname
	*/
	private $_entriesByName;
	private $_verbosityLevel = JOURNAL_VERBOSITY_REPORT;
	
	/*!
	* @deprecated replaced by a create static method that passes dependencies and invokes the load 
	* step. Also implements a new naming scheme
	*/
	public function __construct()
	{
		//throw new \Exception("Class ".__CLASS_." file: ".__FILE__." Journal constructor not allowed");
		//print "<p>Journal constructor</p>";
		return $this;
	}
	
	public function getEntries(){
		return $this->_entriesList;
	}	
	public function getEntryByName($name)
	{
	    //print "<p>".__METHOD__."($name)</p>";
	    if( (!is_null($this->_entriesByName)) && array_key_exists($name, $this->_entriesByName) )
    		return $this->_entriesByName[$name];
	    if( !in_array($name, $this->_namesList) )
	        throw new \Exception(__METHOD__."($name)");
    	// So what if it does not exist
    	$e = new Entry();
    	$e->loadEntry($this, $name);
    	$ix =array_search($name, $this->_namesList);
    	if( is_null($this->_entriesByName) ) $this->_entriesByName = array();
    	$this->_entriesByName[$name] = $e;
    	$this->_entriesList[$ix] = $e;
    	$e->setPositionInJournal($ix);
	    //print "<p>".__METHOD__."($name) found at $ix</p>";
        return $e;
	}
	public function getEntry($name){
		return $this->getEntryByName($name);
	}
		
	public function getEntriesList()
	{
		return $this->_entriesList;
	}
	/*!
	* Return the full path name of file original.html in the Directory
	* for the journal entry with name $e
	* @param string $e name of journal Entry
	* @return full path name
	*/
	public function entryOriginalFileFullPathName($e)
	{
	//var_dump($e);
		return $this->entriesDir."/".$e."/original.html";
	}
	/*!
	* Get a subset of journal entries that have locations is the given country
	* @para String $country
	* @return Array of Entry objects
	*/
	public function getEntriesForCountry($country)
	{
		$a = Array();
		foreach ($this->_entriesList as $e){
			if ($e->getCountry() == $country)
				$a[] = $e;
		}
		return $a;
	}
	
	/*!
	*  Returns the journals name 
	*/
	public function myName()
	{
		return $this->_name;
	}
	function myTrip(){
	//print "Journal->myTrip ". $this->_trip;
		return $this->_trip;
	}
	/*
	** A private function that does the heavy lifting of loading a journal
	*/
	private function load_it(){
		$debug = true;
		$dir =  $this->entriesDir;
		if ($debug) echo "<h1>Journal->load_it entries dir = [$dir]  </h1>\n";
		//echo $this->entriesDir . "  this is the entries dir<br>";
		$this->_entriesList = array();
		$tmpList = Array();
	 	if (!is_dir($dir)) throw new \Exception("loadJournal dir:<$dir> is not a directory\n");
		if ($debug) echo "Journal->load: is a dir true<br>\n";
		if (($dh = opendir($dir))==NULL) {
			throw new \Exception("loadJournal open dir:<$dir> failed");
		}
		if ($debug) echo "Journal->load : open successful<br>\n";
		$positionIndex = 0;
		while (($file = readdir($dh)) !== false) {
			if (($file == ".") || ($file == "..") || ($file == "Template")||($file == ".DS_Store")){
				/*
				 * do nothing - we expected these and want to skip over them
				 */
			}else if ((filetype($dir."/".$file) == "dir") && (Journal::isValidEntryName($file))){
				//print "<p>will load Entry from $file</p>";
				if ($debug) echo "Journal->load:Entry " . $file . "<br> \n";
				$tmpList[] = $file;
				//print "<p>has loaded Entry from $file</p>";
			}else{
				print "Journal::loadJournal File: $file is not a valid journal entry<br>";
				throw new \Exception("Journal::loadJournal File: $file is not a valid journal entry");
			}
		}
		closedir($dh);
		rsort ($tmpList);
		// print "<h2>Loaded entry names - now load entries</h2>";
		// var_dump($tmpList);exit();
		$this->_namesList = $tmpList;
		foreach ($tmpList as $f){
			$e = new Entry();
			if( $this->_lazy_load ) {
				// print "<p>lazy loading</p>";
		        $this->_entriesList[] = null;
		    }else{
				// print "<p>LAZY will load Entry from $f</p>";
			    $e->loadEntry($this ,  $f);
			    $this->_entriesList[] = $e;
				// var_dump($e);
				// print "<p>LAZY has loaded Entry from $f</p>";
		    }					
		}
		if ($debug) echo '<p>return from JournalEntries</p>';
		if ($debug) echo "<h1>Journal->load   exit</h1>";
		$this->_entriesByName = null;
        if(! $this->_lazy_load ) $this->buildByNameList();
	}
	/*
	** This is intended to be the ONLY public interface for loading a journal
	** @parm $data_path 
	** @parm $trip  The name of the trip 
	** @parm $name	The name of the journal directory - always "journals"
	** @parm $lazy_load Boolean for lazy loading of entries
	*/
	public function loadFromFullPath($data_path, $trip, $lazy_load=false){
		$this->_trip = $trip;
		$this->_name = "journals";
		$this->journalRoot = \Database\Locator::get_instance()->journals_root($trip);
		$this->entriesDir = \Database\Locator::get_instance()->entries_root($trip); 
		$this->_lazy_load = $lazy_load;
		$intro_file_name = \Database\Locator::get_instance()->journal_introfile_path($trip);
		if (file_exists($intro_file_name)){
			$this->_intro_html = file_get_contents($intro_file_name);
		}else{
			$this->_intro_html = "";
		}
		//var_dump($this);exit();
		$this->load_it();
	}
	/*!
	* Builds a list (into _entriesByName) of Entry objects indexed
	* by name
	* 
	*/
    private function buildByNameList()
    {
		/*!
		* @TODO: this is a bug in the making buildByNameList
		*/
		$this->_entriesByName = array();
		$positionIndex = 0;
        foreach ($this->_entriesList as $e)
        {
            $this->_entriesByName[$e->myEntryName] = $e;
			$e->setPositionInJournal($positionIndex++);
        }
    }
    /*!
    * Tests a string to see if it is a valid journal entry name
    * @param string candidate for entry name
    * @return true/false
    */
	private static function isValidEntryName($s)
	{
	
		// test the string for a valid date in the format ddmmyy.
		// moreover it has to be either 2009 2010
		//echo "isValidaDate len: " . strlen($s) . " ";
		//print "isValidJournalEntry: $s<br>";
		if ((strlen($s)) != 6) return false;
		$day = substr($s, 4, 2);
		$month = substr($s, 2, 2);
		$year = substr($s, 0, 2);
		//echo "day: " . $day . " month: " . $month . "year: " . $year;
		if ( ! preg_match("/(0[1-9]|[1-2][0-9]|3[0-1])/", $day)) return false;
		//echo "day passed ";
		if ( ! preg_match("/(0[1-9]|1[0-2])/", $month)) return false;
		//echo "month passed ";
		if ( ! preg_match("/(08|09|10|11)/", $year)) return false; 
		//echo "year passed <br/>";
		return true;	
	}
	public function toString()
	{
	 	foreach ($this->_entriesList as &$entry)
		{
			print "<p>Entry : " . $entry->getEntryDate()  . "</p>";
		}		
	}
	function get_by_index($index){
	    //print "<p>".__METHOD__."(".$index.")</p>";
	    if( is_null($this->_entriesList[$index]) ){
	        $name = $this->_namesList[$index];
    	    //print "<p>".__METHOD__."(".$index.") not found create new name : $name</p>";
			$e = new Entry();
		    $e->loadEntry($this ,  $name);
    	    //print "<p>".__METHOD__."(".$index.") new one is : ".$e->myEntryName." </p>";
		    $this->_entriesList[$index] = $e;
			$e->setPositionInJournal($index);
            $this->_entriesByName[$e->myEntryName] = $e;
	    }else{
	        $e = $this->_entriesList[$index];
	    }
   	    //print "<p>".__METHOD__."(".$index.") r = ". ((is_null($e))? 'null' : 'not null') ." [".$e->myEntryName."] </p>";
   	    //var_dump($e);
	    return $e;
	}
	/*!
	* Returns the the previous journal Entry
	* @param Entry
	* @return Entry prev entry in _entriesList
	*/
	private function iprev(Entry $entry)
	{
		$i = $entry->getPositionInJournal();
		if ($i < (count($this->_entriesList) - 1)) {
			return $this->get_by_index(++$i);
			//return $this->_entriesList[++$i];
		}else{
			return NULL;
		}
		
	}
	/*!
	* Returns the next journal Entry
	* @param Entry
	* @return Entry next entry in _entriesList
	*/
	private function inext(Entry $entry)
	{
	    //print "<p>".__METHOD__."(".$entry->myEntryName.")</p>";
		$i = $entry->getPositionInJournal();
	    //print "<p>".__METHOD__."(".$entry->myEntryName.") i = $i</p>";
		if ($i > 0) {
			$r = $this->get_by_index(--$i);
			//$r = $this->_entriesList[--$i];
    	    //print "<p>".__METHOD__."(".$entry->myEntryName.") r = ". ((is_null($r))? 'null' : "  ".$r->myEntryName. ' not null') ."</p>";
			//var_dump($r);
			return $r;
		}else{
			return NULL;
		}
		
	}
	/*!
	* Returns the next journal Entry
	* @param Entry
	* @return Entry next entry in _entriesList
	*/
	public function next(Entry $entry)
	{
		return $this->inext($entry);
		$next = NULL;
		$i = 0;
		$count = count($this->_entriesList);
		foreach($this->_entriesList as &$tmpEntry) 	{
			if ($entry->myEntryName == $tmpEntry->myEntryName)	{
				return $next;
			}else{
				$i++;
				$next = $tmpEntry;
			}
		}
		throw new \Exception("Entry not member of this Journal");
	}
	/*!
	* Returns the the previous journal Entry
	* @param Entry
	* @return Entry prev entry in _entriesList
	*/
	public function previous(Entry $entry)
	{
		return $this->iprev($entry);
		$prev = NULL;
		$next = NULL;
		$i = 0;
		$count = count($this->_entriesList);
		foreach($this->_entriesList as &$tmpEntry) {
			if ($entry->myEntryName == $tmpEntry->myEntryName) {
				if ($i < $count - 1) {
					//$prev = '../'.$this->_entriesList[$i+1]->myEntryName;
					$prev = $this->_entriesList[$i+1];
				}
				return $prev;
			}
			else {
				$i++;
			}
		}
		throw new \Exception("Entry not member of this Journal");
	}
}



 ?>