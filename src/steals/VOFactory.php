<?php
/*!
** @ingroup ValueObjects
**
** The purpose of this class is to create Value Objects under a variety of circumstances and from a
** variety of sources. In that regard it is a factory class.
**
** I have chosen to use a factory class in order to keep the code in the VOItem base class
** simple and focused on its role as a Value
**
** This factory class is designed to only be used in a <strong>static</strong> manner,
** hence all methods are static methods.
**
** In many cases the factory determines the specific class that needs to be created by using a
** naming convention. Each content item contains a type field, whose values correspond to the
** name of a value class as follows:
**  -   entry --> VOEntry, is a journal entry
**  -   post --> VOPost,  a blog post
**  -   article --> VOArticle,   a self contained article
**
** There is only one case where the factory must be told the type of item to be created and that
** is when creating an empty (or template) of a specific item
**
*/
class VOFactory
{
	/*!
	* Constructor - DO NOT SET UP NULL FIELD VAL:UES WE ARE RELYING ON THE __GET METHOD
	* WHICH ONLY GETS CALLED IS THE PROPERTY DOES NOT EXIST
	*/
	private function __construct()
	{
		// Only used in a static content
	}
	/*!
	* A factory method that knows how to create the correct VO class for the file being loaded.
	*
	* If you want to force the class of the created object use load_from_file after creating the
	* object of the desired class
	*
	* @param $fn fully qualified file name
	* @return one of VOItem, VOPost, VOEntry or VOArticle depending on the type value in the file
	*/
	static function from_file($fn)
	{
		$content = new ContentItem();
		$content->load_from_file($fn);
		$class = "VO".ucfirst($content->get_text('type'));
		//print "<p>factory  type=$class</p>";
		$x = new $class();
		$x->content = $content;
		return $x;
	}
	/*!
	* A factory method that knows how to create the correct VO class from a string in html/php format.
	*
	*
	* @param $str  php/html file in string form
	* @return one of VOItem, VOPost, VOEntry or VOArticle depending on the type value in the file
	*/
	static function from_string($str)
	{
		$content = new ContentItem();
		$content->load_from_string($str);
		$class = "VO".ucfirst($content->get_text('type'));
		//print "<p>factory  type=$class</p>";
		$x = new $class();
		$x->content = $content;
		return $x;
	}
	/*!
	* A factory method that knows how to create the correct VO class from an old style Entry object.
	*
	* @param Entry  oldstyle Entry object
	* @return one of VOItem, VOPost, VOEntry or VOArticle depending on the type value in the file
	*/
	static function from_oldstyle_entry($e)
	{
		return self::from_string(self::string_from_oldstyle_Entry($e));
	}
	/*!
	* Create an empty or template of an item and save that template to the given content directory.
	* @param $class_name    The name of the class of the item to be created. This specifies the type of content item.
	* @param $slug          The unique ID of the item being created
	* @param $content_dir   The name of the directory in which the item is to be stored
	*/
	static function create_empty($class_name, $slug, $content_dir)
	{
		//print "<p>".__METHOD__."($class_name, $slug, $content_dir)</p>";
		$fields = $class_name::get_fields();
		//var_dump($fields);
		$typ = $class_name::$typ;
		$file_name = $content_dir."/$slug/content.php";
		ob_start();
		print "<!DOCTYPE html>
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">
<head></head>
<body>
    ";
		foreach ($fields as $f => $v) {
			if ($f == 'slug')
				print "\t<div id=\"$f\">".$slug."</div>\n";
			elseif ($f == 'type')
				print "\t<div id=\"$f\">".$typ."</div>\n";
			elseif ($v == "has")
				;
			else print "\t<div id=\"$f\">".(($f == 'slug')? $slug : "ABCDEFG") ."</div>\n";
		}
		print "
</body>
</html>
    ";
		$s = ob_get_clean();
		//var_dump($s);
		//var_dump($file_name);
		//ItemUtes::make_file($file_name);
		//print "<h3>about the mkdir $content_dir/$slug</h3>";
		//$r = system("mkdir -p -v -m 0777 ".$content_dir."/$slug ");
		//print "<h3>after the mkdir $r</h3>";
		mkdir($content_dir."/$slug", 511, true);
		chmod($content_dir."/$slug", 511);
		file_put_contents($file_name, $s);
		chmod($file_name, 511);
		$r = self::from_file($file_name);
		var_dump($r);
		return self::from_file($file_name);
	}
	/*!
	* Companion function to string_from_oldstyle_Entry. Outputs a div with id and
	* text value
	* @param $id  the value of the divs id attribute
	* @param $value the text that will go inside the div
	* @return prints result into the output buffer
	*/
	static function make_div($id, $value)
	{
		print "\t<div id='$id'>$value</div>\n";
	}
	/*!
	* Companion function to string_from_oldstyle_Entry. Outputs a div with id and
	* php/html content value
	* @param $id  the value of the divs id attribute
	* @param $value the html/php text that will go inside the div
	* @return prints result into the output buffer
	*/
	static function make_big_div($id, $value)
	{
		$v = trim($value);
		if (strlen($v) == 0) {
			print "\t<div id='$id'></div>\n";
		} else {
			print "\t<div id='$id'>\n";
			print "\t\t".$v."\n";
			print "\t</div>\n";
		}
	}
	/*!
	* Converts an oldstyle Entry object into a php string in the new format.
	*
	* @param Entry object (oldstyle)
	* @return string, php new format
	*/
	static function string_from_oldstyle_Entry($e, $to_trip = null)
	{
		$camp = $e->getEntryCamping();
		$miles = $e->getEntryMiles();
		$m = preg_replace("/[^0-9]*/", "", $miles);
		$trip = (is_null($to_trip)) ? $e->myTripName: $to_trip;
		// print "<p>".__METHOD__."</p>";
		// var_dump($m);
		// print "<p>".__METHOD__."</p>";
		ob_start();
		print "<!DOCTYPE html>
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">
<head></head>
<body>
    ";
		print "<!-- common properties -->\n";
		self::make_div("version", "1.0");
		self::make_div("type", "entry");
		self::make_div("slug", $e->myEntryName);
		self::make_div("status", "draft");
		self::make_div("creation_date", $e->myEntryName);
		self::make_div("published_date", $e->myEntryName);
		self::make_div("last_modified_date", $e->myEntryName);
		self::make_div("trip", $trip);
		self::make_big_div("title", trim($e->getEntryAbstract()));
		self::make_big_div("abstract", $e->getEntryAbstractHTML());
		print "<!-- common properties -->\n";
	
		print "<!-- journal entry properties -->\n";
		self::make_div("entry_date", $e->myEntryName);
		self::make_div("day_number", preg_replace("/[^0-9]*/", "", $e->getEntryDayNumber()));
		self::make_div("place", $e->getEntryPlace());
		self::make_div("country", $e->getCountry());
		self::make_div("miles", $m);
		self::make_div("odometer", "123456");
		self::make_div("latitude", $e->getEntryLatDD_DDD());
		self::make_div("longitude", $e->getEntryLngDD_DDD());
		//make_div("altitude");
		print "<!-- journal entry properties -->\n";
	
		$c = $e->getEntryCamping();
		EntryModifier::create($e)->removeCamping();
		$mc = $e->getEntryText();
		$mc_fixed = EntryModifier::create($e)->fixedMainContent();
		//self::make_big_div("main_content",$e->getEntryText());
		self::make_big_div("main_content", $mc_fixed);
		
		if (!is_null($c)) {
			self::make_big_div("camping", $c);
		} else {
			self::make_big_div("camping", " ");
		}
		self::make_big_div("border", " ");
		print "
</body>
</html>
    ";
		$s = ob_get_clean();
		return $s;
	}
}
class EntryModifier
{
	static function create(Entry $e)
	{
		$n = new EntryModifier();
		$n->_doc = $e->myDocument;
		$n->entry = $e;
		return $n;
	}
	function fixedMainContent()
	{
		$mc = $this->entry->getEntryText();
		$c = preg_replace(
			"/\<\?php[ ]*JournalGalleryShow\([ ]*\)[ ]*;[ ]*\?\>/",
			"<?php Skin::JournalGalleryThumbnails(\$trip, \$entry); ?>",
			$mc
		);
		return $c;
	}
	function removeCamping()
	{
		$camp_uc = $this->_doc->getElementById('Camping');
		if (!is_null($camp_uc)) {
			$p = $camp_uc->parentNode;
			$p->removeChild($camp_uc);
			//$fc = $ci->_doc->saveHTML();
			//file_put_contents($items_dir."/".$iname."/content.php", $fc);
			//var_dump($ci->_doc->saveHTML($camp_uc));
		}
	}
}
