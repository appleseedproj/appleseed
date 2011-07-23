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

/** Filesystem Hook Class
 * 
 * Filesystem Hook Class
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cFilesystemHook extends cHook {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function OnStoreData ( $pData = null ) {
		
		$contents = $pData['Contents'];
		$component = $pData['Component'];
		$area = $pData['Area'];
		$filename = $pData['Filename'];
		$directory = $pData['Directory'];
		$overwrite = $pData['Overwrite'];
		
		$Dir = ASD_PATH . '_storage/components/' . $component . '/' . $area . '/' . $directory;
		
		// If the directory doesn't exist, create it.
		if ( !is_dir ( $Dir ) )
			rmkdir ( $Dir );
		
		$File = $Dir . '/' . $filename;
		
		if ( ( file_exists ( $File ) ) && ( $overwrite ) ) {
			return ( $this->_Store ( $contents, $File ) );
		} else if ( file_exists ( $File ) ) {
			return ( true );
		} else {
			return ( $this->_Store ( $contents, $File ) );
		}
		
	    return ( false );
	}
	
	private function _Store ( $pContents, $pLocation ) {
		
		if ( strlen ( $pContents ) == 0 ) return ( false );
		
		$fp = fopen($pLocation, 'wb');
		fwrite($fp, $pContents);
		fclose($fp);
		
		return ( true );
	}
	
	public function OnFileExists ( $pData = null ) {
		
		$component = $pData['Component'];
		$area = $pData['Area'];
		$filename = $pData['Filename'];
		$directory = $pData['Directory'];
		
		$Dir = ASD_PATH . '_storage/components/' . $component . '/' . $area . '/' . $directory;
		
		// If the directory doesn't exist, create it.
		if ( !is_dir ( $Dir ) )
			rmkdir ( $Dir );
		
		$File = $Dir . '/' . $filename;
		
		$exists = file_exists ( $File );
		
		return ( $exists );
	}

	public function OnScanDirectory ( $pData = null ) {
		$Directory = $pData['Directory'];
		$Recursive = $pData['Recursive'];

		if ( $Recursive == true ) {
			$return = rscandir ( $Directory );
		} else {
			$return = scandir ( $Directory );
		}

		return ( $return );
	}
}
