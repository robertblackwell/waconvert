<?php
/*!
 * @author    Robert Blackwell rob@whiteacorn.com
 * @copyright whiteacorn.com
 * @license   MIT License
 */
 /*! @ingroup Extensions
 * This class extends the standard string class to add a couple of extra methods that I like to have available
 *
 */
class ExtendedString
{
	private $str;
	public static function create($element)
	{
		$obj = new ExtendedString();
		$obj->str = $element;
		return $obj;
	}
	public static function hexChar($ch)
	{
		for ($c = 0; $c <= 255; $c++)
			if (chr($c) == $ch)
				return $c;
		
	}
	/*!
	 * isAlphaNumeric
	 * Returns true if the string is alpha numeric
	 * @return string
	*/
	public static function isAlphaNumeric($ch)
	{
		if ( 
		   (('a' <= $ch) && ($ch <= 'z'))
		|| (('A' <= $ch) && ($ch <= 'Z'))
		|| (('0' <= $ch) && ($ch <= '9'))
		// || (('<' == $ch) || ($ch == '>') || ($ch == '/'))
		)
			return true;
		return false;
		
	}
	/*!
	 * hex 
	 *
	 * Returns the strings hexidecimal representation.
	 * @return string
	*/
	function hex()
	{
		$s = $this->str;
		print "hex: length : " . strlen($s);
		$out = "";
		for ($i = 0; $i < strlen($s); $i++){
			$ch = $s[$i];
			if (ExtendedString::isAlphaNumeric($ch)){
				$out = $out . $ch;
			}else
				$out = $out . sprintf("(%02d)", ExtendedString::hexChar($ch));
		}
		return $out;
	}
	/*!
	 * removeNulls
	 * Removes null chr(0) characters from a string
	 * @return string 
	*/
	function removeNulls()
	{
		$s = $this->str;
		$out = "";
		$j = 0;
		for ($i = 0; $i < strlen($s); $i++){
			$ch = $s[$i];
			if ($ch > chr(31)){
				$out = $out . $ch;
			}	
		}
		return $out;	
	}
}
?>