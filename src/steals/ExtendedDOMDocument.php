<?php
/*!
 *
 * @author    Robert Blackwell rob@whiteacorn.com
 * @copyright whiteacorn.com
 * @license   MIT License
 * @defgroup Extensions
 * these classes provide extension or alternatives to standard PHP classes. They are conveniences only.
 */
/*! @ingroup Extensions
 * This class extends the DOM class DOMDocument to add a couple of extra methods that I loke to have available
 *
*/
class ExtendedDOMDocument
{
	private $_document;
	/*!
	* create
	* Creates a new ExtendedDOMNode from a standard DOMNode
	* @param DOMNode $node
	* @return ExtendedDOMNode
	*/
	public static function create($doc)
	{
		print "ExtendedDOMDocument::create \n";
		var_dump($doc);
		$obj = new ExtendedDOMDocument();
		$obj->_document = $doc;
		return $obj;
	}
	/*!
	* Creates a new node with the give tag and creates under it the children necessary to implement the html text
	* apssed in.
	* @param String  $tag
	* @param String  $html
	* @return DOMNode
	*/
	function createElementFromHTML($tag, $html)
	{
		//print "ExtendedDOMDocument::createElementFromHTML ($tag, $html) \n";
		//var_dump($this->_document);
		$d = new DOMDocument();
		$d->loadHTML($html);
		$body = $d->getElementsByTagName("body")->item(0);
		$el = $this->_document->createElement($tag);
		foreach ($body->childNodes as $child){
			$child = $this->_document->importNode($child, true);
			$child = $el->appendChild($child);
		}
		return $el;
	}
}
?>