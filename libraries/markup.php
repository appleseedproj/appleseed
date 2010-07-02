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

//require_once ( ASD_PATH . DS . 'libraries' . DS . 'external' . DS . 'QueryPath-2.0.1' . DS . 'QueryPath.php' );
require_once ( ASD_PATH . DS . 'libraries' . DS . 'external' . DS . 'SimpleHTMLDom-1.11' . DS . 'simple_html_dom.php' );

/** Markup Class
 * 
 * Handles basic markup 
 * 
 * @package     Appleseed.Framework
 * @subpackage  Library
 */
class cMarkup extends simple_html_dom {
	
	private $_Segment;
	
	private $_CurrentSegment;

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
	}

	public function Load ( $pData, $pSegment = null ) {
		
		parent::load ( $pData );
		
		$pSegment = str_replace ( '.php', '', $pSegment );
		
		if ( !$pSegment ) $pSegment = $this->_CurrentSegment;
		
		$this->_Segment[$pSegment] = $this->save();
		
		$this->_CurrentSegment = $pSegment;
		
		return ( true );
	}
	
	public function Modify ( $pSelector, array $pValues, $pSegment = null ) {

		$pSegment = str_replace ( '.php', '', $pSegment );
		
		if ( !$pSegment ) $pSegment = $this->_CurrentSegment;
		
		$element = $this->find($pSelector, 0);
		
		foreach ( $pValues as $v => $value ) {
			$element->$v = $value;
		}
		
		$this->_Segment[$pSegment] = $this->save();
		
		$this->_CurrentSegment = $pSegment;
		
		return ( true );
	}
	
	public function RemoveElement ( $pSegment, $pSelector ) {
	}
	
	public function Display ( $pSegment = null ) {
		
		$pSegment = str_replace ( '.php', '', $pSegment );
		
		if ( !$pSegment ) $pSegment = $this->_CurrentSegment;
		
		// In case it was accidentally specificied, remove the php extension from view name.
		$pSegment = str_replace ( '.php', '', $pSegment );
		
		echo $this->_Segment[$pSegment];
		
		$this->_CurrentSegment = $pSegment;
		
		return ( true );
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
	 * @access  public
	 */
	public function __construct() {       
		parent::__construct();
	}
	
	public function AddOption ( $pSegment, $pSelector, array $pValues ) {
	}
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function SetValue ( $pSegment, $pName, $pValue, $pUseRequest = true ) {
	}
	
	public function DisableElement ( $pSegment, $pSelector ) {
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
	 * @access  public
	 */
	function __construct() {       
		parent::__construct();
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
	 * @access  public
	 */
	function __construct() {       
		parent::__construct();
	}

}
