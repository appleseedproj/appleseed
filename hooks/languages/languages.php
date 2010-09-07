<?php
/**
 * @version      $Id$
 * @package      Appleseed.Framework
 * @subpackage   System
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Languages Hook Class
 * 
 * Languages Hook Class
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cLanguagesHook extends cHook {
	
	private $_Translations;

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function OnLoadLanguage ( $pData = null ) {
		
		$store = $pData['store'];
		$data = $pData['data'];
		
		list ( $scope, $component, $ext ) = explode ( '.',  $store );
		
		$found_js = false;
		foreach ( $data as $key => $value ) {
			if ( preg_match ( "/^JS/", $key ) ) {
				$found_js = true;
			}
		}
		
		
		if ( $found_js ) {
			$oldData = $data;
			$data = array ();
			foreach ( $oldData as $key => $value ) {
				if ( preg_match ( "/^JS/", $key ) ) {
					$data[$key] = $value;
				}
			}
		}
		
		// Removed, json options only available in 5.3
		// $this->_Translations[$scope][$component] = json_encode ( $data, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP );
		
		foreach ( $data as $d => $dat ) {
			$data[$d] = htmlspecialchars ( $dat );
		}
		$this->_Translations[$scope][$component] = json_encode ( $data );
		
	}
	
	public function OnSystemEnd ( $pData = null ) {
		
		echo "\n";
		echo '<dfn id="appleseed-language" style="display:none; width:1px; height:1px;">' . "\n\n";

		foreach ( $this->_Translations as $type => $translations ) {
			if ( $type == "_system" ) $type = 'document';
			echo "\t" . '<var id="appleseed-language-type-' . $type . '">' . "\n";
			foreach ( $translations as $t => $translation ) {
			
				echo "\t\t" . '<var id="appleseed-language-' . $type . '-' . $t . '">' . $translation . '</var>' . "\n\n";
			}
			echo "\t" . '</var>' . "\n";
		}
		
		echo "</dfn>";
		
		return ( false );
	}
	
}
