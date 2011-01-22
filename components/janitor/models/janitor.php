 <?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Janitor
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Friends Component Circles Model
 * 
 * Friends Component Circles Model Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Janitor
 */
class cJanitorModel extends cModel {
	
	protected $_Tablename = "Janitor";
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( $pTables = null ) {       
		parent::__construct( $pTables );
	}
}
 