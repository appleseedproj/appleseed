<?php
/**
 * @version      $Id$
 * @package      Appleseed.Framework
 * @subpackage   Library
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Storage Class
 * 
 * Handles basic file storage and retrieval
 * 
 * @package     Appleseed.Framework
 * @subpackage  Library
 */
class cStorage {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
	}
	 
	public function SaveImage ( $pResource, $pComponent, $pArea, $pDirectory, $pFilename, $pOverwrite = true, $pType = IMAGETYPE_JPEG ) {
		eval ( GLOBALS );
		
		ob_start();
		
		switch ( $pType ) {
			case IMAGETYPE_JPEG:
			default:
				imagejpeg ( $pResource );
			break;
		}
		
		$contents = ob_get_clean();
		
		$pData['Contents'] = $contents;
		$pData['Component'] = $pComponent;
		$pData['Area'] = $pArea;
		$pData['Filename'] = $pFilename;
		$pData['Directory'] = $pDirectory;
		$pData['Overwrite'] = $pOverwrite;
		
		$return = $zApp->GetSys ( 'Event' )->Trigger ( 'On', 'Store', 'Data', $pData );
		
		return ( $return );
	}
	
	public function Exists ( $pComponent, $pArea, $pDirectory, $pFilename ) {
		eval ( GLOBALS );
		
		$pData['Component'] = $pComponent;
		$pData['Area'] = $pArea;
		$pData['Filename'] = $pFilename;
		$pData['Directory'] = $pDirectory;
		
		$return = $zApp->GetSys ( 'Event' )->Trigger ( 'On', 'File', 'Exists', $pData );
		
		return ( $return );
	}

	public function Scan ( $pDirectory ) {
		eval ( GLOBALS );

		$Data = array();

		$Data['Directory'] = $pDirectory;

		$files = $zApp->GetSys ( 'Event' )->Trigger ( 'On', 'Scan', 'Directory', $Data );

		foreach ( $files as $f => $file ) {
			if ( $file == '.' ) continue;
			if ( $file == '..' ) continue;

			$return[] = $file;
		}
		
		return ( $return );
	}
	
}
