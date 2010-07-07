<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Example
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Example Component Model
 * 
 * Example Component Model Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Example
 */
class cExampleModel extends cModel {
	
	protected $_Tablename = "ExampleCustomers";
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( $pTables = null ) {       
		parent::__construct( $pTables );
	}
	
}

/** Example Component Map Model
 * 
 * Example Component Map Model Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Example
 */
class cExampleEmployeesModel extends cModel {
	
	protected $_Tablename = "ExampleEmployees";
	
}

/** Example Component Tags Model
 * 
 * Example Component Tags Model Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Example
 */
class cExampleOfficesModel extends cModel {
	
	protected $_Tablename = "ExampleOffices";
	
}