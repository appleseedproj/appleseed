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

/** Image Class
 * 
 * Handles image manipulation
 * 
 * @package     Appleseed.Framework
 * @subpackage  Library
 */
class cImage {

  	/*
 	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
	}
        
	public function ResizeAndCrop ($pResource, $pNewWidth, $pNewHeight ) {
		
		$originalWidth = imagesx ( $pResource  );
		$originalHeight = imagesy ( $pResource  );
		
		if ( $originalHeight == $originalWidth ) {
			// Proportion is the same
			$newwidth = $pNewWidth; $newheight = $pNewHeight;
			$startx = 0; $starty = 0;
			$endx = $pNewWidth; $endy = $pNewHeight;
		} elseif ( $originalHeight > $originalWidth ) {
			// Proportion is vertical
			$newwidth = $pNewWidth;
			$newheight = ( $pNewWidth / $originalWidth ) * $originalHeight;
			$newheight = floor ( $newheight );
			$startx = 0; $starty = floor( ( ( $newheight - $pNewHeight ) / 2 ) );
			$endy = $pNewWidth; $endy = $newheight - ceil ( ( ( $newheight - $pNewHeight ) / 2 ) );
		} else {
			// Proportion is horizontal
			$newwidth = ( $pNewHeight / $originalHeight ) * $originalWidth;
			$newwidth = floor ( $newwidth );
			$newheight = $pNewHeight;
			$startx = floor ( ( ( $newwidth - $pNewWidth ) / 2 ) );  $starty = 0;
			$endx = $newwidth - ceil ( ( ( $newwidth - $pNewWidth ) / 2 ) );  $endy = $pNewHeight;
		} // if
		
		/* echo $originalWidth, '<br />'; echo $originalHeight, '<br /><br />'; echo $pNewWidth, '<br />'; echo $pNewHeight, '<br /><br />'; echo $newwidth, '<br />'; echo $newheight, '<br /><br />'; echo $startx, '<br />'; echo $starty, '<br />'; echo $endx, '<br />'; echo $endy, '<br />'; exit; */
		  
		$src_img = imagecreatetruecolor ( $originalWidth, $originalHeight );
		imagecopy( $src_img, $pResource, 0, 0, 0, 0, $originalWidth, $originalHeight );

		$intermediary = imagecreatetruecolor ( $newwidth, $newheight );
		$result = imagecreatetruecolor ( $pNewWidth, $pNewHeight );

		// Resize image.
		imagecopyresampled ( $intermediary, $src_img, 0, 0, 0, 0, $newwidth, $newheight, $originalWidth, $originalHeight );
		
		// Crop image.
		imagecopy ( $result, $intermediary, 0, 0, $startx, $starty, $pNewWidth, $pNewHeight );

		imagedestroy ( $intermediary );
		
		return ( $result );
	} // ResizeAndCrop
	
}
