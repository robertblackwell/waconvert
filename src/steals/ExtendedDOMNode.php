<?php
/*!
 * @author    Robert Blackwell rob@whiteacorn.com
 * @copyright whiteacorn.com
 * @license   MIT License
 */
/*! @ingroup Extensions
 * This class extends the DOM class DOMNode to add a couple of extra methods that I loke to have available
 *
*/
class ExtendedDOMNode
{
	private $_node;
	/*!
	* create
	* Creates a new ExtendedDOMNode from a standard DOMNode
	* @param DOMNode $node
	* @return ExtendedDOMNode
	*/
	public static function create($node)
	{
		$obj = new ExtendedDOMNode();
		$obj->_node = $node;
		return $obj;
	}
	/*!
	* hex
	* Returns the inner html text of the ExtendedDOMNode as a hexidecimal string
	* @return string
	*/
	function hex()
	{
		$s = $this->innerHTML();
		//print "hex: length : " . strlen($s);
		$out = "";
		for ($i = 0; $i < strlen($s); $i++)
		{
			$ch = $s[$i];
			$out = $out . sprintf("%d ", $ch);
		}
		return $out;
	}
	/*!
	* fullHTML
	* Returns the full html text of the ExtendedDOMNode
	* @return string
	*/
	public function fullHTML()
	{
		$doc = new DOMDocument();
		$doc->appendChild($doc->importNode($this->_node, true));
		return $doc->saveHTML();
	
	}
	/*
	* fullHTML
	* Returns the inner html text of the ExtendedDOMNode. That is the html for all child nodes
	* @return string
	*/
	public function innerHTML()
	{
		$doc = new DOMDocument();
		foreach ( $this->_node->childNodes as $child)
		{
			$doc->appendChild($doc->importNode($child, true));
		}
		return $doc->saveHTML();
	}
	/*!
	* Appends a new child node (and its children) that is created from the tag and html
	* @param String  $tag
	* @param String  $html
	* @return  DOMNode  the node appended
	*/
	public function appendChildFromHTML($tag, $html){
		//print "ExtendedDOMNode::appendChildFromHTML  ($tag  $html)\n";
		$d = $this->_node->ownerDocument;
		var_dump($this->_node->ownerDocument);
		$xd = ExtendedDOMDocument::create($d);
		$n = $xd->createElementFromHTML($tag, $html);
		$n = $this->_node->appendChild($n);
		return $n;
	}
	/*!
	* Sets the inner HTML for this node to the given string
	* apssed in.
	* @param String  $tag
	* @param String  $html
	* @return DOMNode
	*/
	public function setInnerHTML($html)
	{
		if ($debug) print "ExtendedDOMNode::setInnerHTML  this= ". $this->innerHTML() ."  html: $html\n";
		$newEl = ExtendedDOMDocument::create($this->_node->ownerDocument)->createElementFromHTML("div",$html);
		$n = $this->_node;
		while($n->hasChildNodes()){
			if (!($tmp = $n->removeChild($n->firstChild))) die("ExtendedDOMNode::setInnerHTML - remove failed");
		}
		foreach ($newEl->childNodes as $nChild){
			if(!($cn = $this->_node->appendChild($this->_node->ownerDocument->importNode($nChild->cloneNode(true))))) 
				die("ExtendedDOMNode::setInnerHTML - append failed");			
		}
	}
	/*!
	* Sets the outer HTML for this node to the given string
	* apssed in.
	* @param String  $tag
	* @param String  $html
	* @return DOMNode
	*/
	public function setOuterHTML($html)
	{
		if ($debug) print "ExtendedDOMNode::setInnerHTML  this= ". $this->innerHTML() ."  html: $html\n";
		$newEl = ExtendedDOMDocument::create($this->_node->ownerDocument)->createElementFromHTML("div",$html);
		$n = $this->_node;
		$p = $n->parentNode;
		$d = $n->ownerDocument;
		foreach ($newEl->childNodes as $nChild){
			$cc = $nChild->cloneNode(true); 
			$cc = $d->importNode($cc);
			$cn = $p->insertBefore($cc, $n);
			if(!($cn)) 
				die("ExtendedDOMNode::setOuterHTML - insertBefore failed");			
		}
		if(!$p->removeChild($n)) die("ExtendedDOMNode::setOuterHTML - remove failed");
	}
	public function __set($name, $value){
		if ($debug) print "XXXXXXXXXXXX__set called  name : $name value : $value\n";
		if ($name == "outerHTML")
			return $this->setOuterHTML($value);
		else if ($name == "innerHTML")
			return $this->setInnerHTML($value);
		die("ExtendedDOMNode set property $name unsupported");
	}
	public function __get($name){
		if ($debug) print "XXXXXXXXXXX__get called  name : $name \n";
		if ($name == "outerHTML")
			return $this->outerHTML();
		else if ($name == "innerHTML")
			return $this->innerHTML();
		die("ExtendedDOMNode get property $name unsupported");
	}
}
?>