<?php
/**
 * @author Michael Chisari <michael.chisari@gmail.com
 * @version 0.7.4
 * @package system/language
 * @copyright Copyright (C) 2004 - 2010 The Appleseed Project. All rights reserved.
 * @license GNU General Public License
 */
 
 class cLanguage {
 	
 	/**
 	 * @access public
 	 * @param string The untranslated string
 	 */
 	function _ ($pString) {
 		$pString .= "!";
 		return ($pString);
 	}
 }

 function __ ($pString) {
     return cLanguage::_ ($pString);   
 }