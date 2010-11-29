<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Search
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Search Component
 * 
 * Search Component Entry Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Search
 */
class cSearch extends cComponent {
	
	private $_Cache;
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function Index ( $pData = null ) {
		
		$return = $this->Load ( 'Search', null, 'Index', $pData );
		
		return ( $return );
	}
	
}
