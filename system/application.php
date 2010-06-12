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

require_once ( ASD_PATH . DS . 'system' . DS . 'router.php' );

/** Application Class
 * 
 * Appleseed Application class
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cApplication {

        /**
         * Constructor
         *
         * @access  public
         */
        public function __construct ( ) {       
        }
        
        function Initialize ( ) {
        	$this->Router = new cRouter ( );
        } 
        
}
