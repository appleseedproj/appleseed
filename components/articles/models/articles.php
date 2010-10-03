<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Articles
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Articles Component Circles Model
 * 
 * Articles Component Circles Model Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Articles
 */
class cArticlesModel extends cModel {
	
	protected $_Tablename = "contentArticles";
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( $pTables = null ) {       
		parent::__construct( $pTables );
	}
	
	public function GetArticles() {
		
		$this->Retrieve ( array ( "Verification" => 1 ) );
		
	}
	
}
