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

	protected $_Resource;
	protected $_Width;
	protected $_Height;
	protected $_Type;

  	/*
 	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
	}
        
    // Set the attributes for an image.
    public function Attributes ($pFilename) {

      // Retrieve file attributes.
      list($width, $height, $type, $attr) = getimagesize($pFilename);

      // Assign Values
      $this->_Width = $width;
      $this->_Height = $height;
      $this->_Type = $type;

      return ( true );
    } // Attributes

    // Convert the file to a an image resource.
    public function Convert ($pFilename) {

      // Retrieve file attributes.
      list($width, $height, $type, $attr) = getimagesize($pFilename);

      // Determine which type of file to convert from.
      switch ($type) {
        case IMAGETYPE_PNG:
          $src_img = imagecreatefrompng ($pFilename);
        break;

        case IMAGETYPE_WBMP:
          $src_img = imagecreatefromwbmp ($pFilename); 
        break;

        case IMAGETYPE_JPEG:
          $src_img = imagecreatefromjpeg ($pFilename); 
        break;

        case IMAGETYPE_GIF:
          $src_img = imagecreatefromgif ($pFilename); 
        break;
      } // switch

      // Copy the source image.
      $this->_Resource = imagecreatetruecolor ($width, $height);
      $result = imagecopy($this->_Resource,$src_img,0,0,0,0,$width,$height);

      return ( $result );

    } // Convert

    // Resize an image.
    public function Resize ($pNewWidth, $pNewHeight, $pProportional = TRUE, $pXOnly = FALSE, $pYOnly = FALSE) {

      // Calculate the proportional new height and width.
      if ($pProportional) {
        if ($pNewWidth && ($this->_Width < $this->_Height) && 
                          (!$pXOnly) || ($pYOnly)      ) {
             $pNewWidth = ($pNewHeight / $this->_Height) * $this->_Width;
             $pNewWidth = floor ($pNewWidth);
        } else {
             $pNewHeight = ($pNewWidth / $this->_Width) * $this->_Height;
             $pNewHeight = floor ($pNewHeight);
        } // if
      } // if

      $src_img = imagecreatetruecolor ($this->_Width, $this->_Height);
      imagecopy($src_img, $this->_Resource, 0, 0, 0, 0, $this->_Width, $this->_Height);

      $this->Destroy ();

      $this->_Resource = imagecreatetruecolor ($pNewWidth, $pNewHeight);

      $result = imagecopyresampled($this->_Resource, $src_img, 0, 0, 0, 0, $pNewWidth, $pNewHeight, $this->_Width, $this->_Height);
      // int imagecopyresized(int dst_im, int src_im, int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h) 

      $this->_Width = $pNewWidth;
      $this->_Height = $pNewHeight;

      return ( $result );

    } // Resize
    
	public function ResizeAndCrop ($pNewWidth, $pNewHeight ) {
		
		$originalWidth = imagesx ( $this->_Resource  );
		$originalHeight = imagesy ( $this->_Resource  );
		
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
		imagecopy( $src_img, $this->_Resource, 0, 0, 0, 0, $originalWidth, $originalHeight );

		$intermediary = imagecreatetruecolor ( $newwidth, $newheight );

		$this->Destroy();
		$this->_Resource = imagecreatetruecolor ( $pNewWidth, $pNewHeight );

		// Resize image.
		imagecopyresampled ( $intermediary, $src_img, 0, 0, 0, 0, $newwidth, $newheight, $originalWidth, $originalHeight );
		
		// Crop image.
		imagecopy ( $this->_Resource, $intermediary, 0, 0, $startx, $starty, $pNewWidth, $pNewHeight );

		imagedestroy ( $intermediary );

		return ( true );
	} // ResizeAndCrop

    // Save the image resource to a file.
    public function Save ( $pFilename, $pType = IMAGETYPE_JPEG) {

      // Delete the old file if it exists.
      if (file_exists ($pFilename) ) {
        // note: Permission Denied error.
        unlink ($pFilename);
      } // if

      // Determine which type of file to save to.
      switch ($pType) {
        case IMAGETYPE_PNG:
          $result = imagepng($this->_Resource, $pFilename);
        break;

        case IMAGETYPE_WBMP:
          $result = imagewbmp($this->_Resource, $pFilename);
        break;

        case IMAGETYPE_JPEG:
          $result = imagejpeg($this->_Resource, $pFilename, 100);
        break;

        case IMAGETYPE_GIF:
          $result = imagegif($this->_Resource, $pFilename);
        break;
      } // switch

      // @note: Probably not the best way to do this.
      chmod ($pFilename, 0777);

      // Save the image resource .
      if (!$result) {
        return ( false );
      } // if
     
      return ( true );
    } // Save

    // Destroy the image resource.
    public function Destroy () {

      imagedestroy ($this->_Resource);

    } // Destroy

	public function Get ( $pVariable ) {
		$var = '_' . $pVariable;

		return ( $this->$var );
	}
	
}
