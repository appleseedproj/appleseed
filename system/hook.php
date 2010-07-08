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

/** Hook Class
 * 
 * Base class for Hooks
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cHook extends cBase {
	
	protected $_Component;
	protected $_Hook;

        /**
         * Constructor
         *
         * @access  public
         */
        public function __construct ( ) {       
        	$rc = new ReflectionClass ( get_class ( $this ) );
            $location = dirname ( $rc->getFileName() );
            
            unset ( $rc );
        	
			list ( $null, $hookdata ) = explode ( 'hooks/', $location );
		
			list ( $this->_Component, $this->_Hook ) = explode ( '/', $hookdata );
        }

}
