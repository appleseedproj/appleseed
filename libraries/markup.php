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

/** Markup Class
 * 
 * Handles basic markup 
 * 
 * @package     Appleseed.Framework
 * @subpackage  Library
 */
class cMarkup {

        /**
         * Constructor
         *
         * @access  public
         */
        public function __construct ( ) {       
        }

}

/** HTML Class
 * 
 * Handles basic html markup 
 * 
 * @package     Appleseed.Framework
 * @subpackage  Library
 */
class cHTML extends cMarkup {

        /**
         * Constructor
         *
         * @access  protected
         */
        function __construct() {       
        }

}

/** HTML Class
 * 
 * Handles basic xml markup 
 * 
 * @package     Appleseed.Framework
 * @subpackage  Library
 */
class cXML extends cMarkup {

        /**
         * Constructor
         *
         * @access  protected
         */
        function __construct() {       
        }

}

/** RSS Class
 * 
 * Handles basic rss xml markup 
 * 
 * @package     Appleseed.Framework
 * @subpackage  Library
 */
class cRSS extends cXML {

        /**
         * Constructor
         *
         * @access  protected
         */
        function __construct() {       
        }

}
