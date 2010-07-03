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
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
	}

	public function Load ( $pData ) {
		
		parent::load ( $pData );
		
		return ( true );
	}
	
	public function Modify ( $pSelector, array $pValues ) {

		$element = $this->Find($pSelector, 0);
		
		foreach ( $pValues as $v => $value ) {
			$element->$v = $value;
		}
		
		return ( true );
	}
	
	public function RemoveElement ( $pSelector ) {
		
		$element = $this->Find($pSelector, 0);
		
		$element->outertext = "";
		
		return ( true );
		
	}
	
	public function Display () {
		
		echo $this->Save();
		
		return ( true );
		
	}
	
	function Reload ( ) {
		
		$this->Load ( $this->Save () );
		
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
	
	public function AddOptions ( $pSelector, array $pValues ) {
		
		foreach ( $pValues as $value => $label ) {
			$option = '<option value="%s">%s</option>';
			$this->Find($pSelector, 0)->innertext .= sprintf ( $option, $value, $label );
			
		}
		
		$this->Reload();
		
		return ( true );
	}
	
	/**
	 * Synchronize the values with the Request data.
	 *
	 * @access  public
	 */
	public function Synchronize ( $pDefaults = array() ) {
		
		$inputs = $this->Find("[name=]");
		
		// Set all request variable names to lower case
		foreach ( $_REQUEST as $r => $request ) {
			$r = strtolower ( ltrim ( rtrim ( $r ) ) );
			$requests[$r] = $request; 
		}
		
		// Set all default variable names to lower case
		foreach ( $pDefaults as $d => $default ) {
			$d = strtolower ( ltrim ( rtrim ( $d ) ) );
			$defaults[$d] = $default; 
		}
		
		
		// Loop through the named input tags
		foreach ( $inputs as $i => $input ) {
			$assign = null;
			$name = strtolower ( ltrim ( rtrim ( $input->name ) ) );
			$tag = strtolower ( ltrim ( rtrim ( $input->tag ) ) );
			$type = strtolower ( ltrim ( rtrim ( $input->type ) ) );
			
			if ( isset ( $requests[$name] ) ) {
				$assign = $requests[$name];
			} elseif ( isset ( $defaults[$name] ) ) {
				$assign = $defaults[$name];
			}
			
			if ( $assign ) {
				// Assign values from $_REQUEST 
				switch ( $tag ) {
					case 'textarea':
						$input->innertext = $assign;
					break;
					case 'select':
						$options = $input->Find("option");
						
						foreach ( $options as $o => $option ) {
							if ( $option->value == $assign ) {
								$option->selected = "selected";
							} elseif ( $option->innertext == $assign ) {
								$option->selected = "selected";
							}
						}
					break;
					default:
						switch ( $type ) {
							case 'checkbox':
								$input->checked = $assign;
							break;
							default:
								$input->value = $assign;
							break;
						}
					break;
				} 
			}
		}
			
		return ( true );
	}
	
	public function DisableElement ( $pSelector ) {
		
		$element = $this->Find($pSelector, 0);
		
		$element->disabled = "disabled";
		
		return ( true );
		
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
