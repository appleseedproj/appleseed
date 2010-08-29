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

require ( ASD_PATH . DS . 'libraries' . DS . 'external' . DS . 'SimpleHTMLDom-1.11' . DS . 'simple_html_dom.php' );

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
		
		$element->clear();
		
		return ( true );
		
	}
	
	public function Display () {
		
		// Add an html element here to have it's innertext modified to use the __ function.
		$translate = array ( 
			"label", 
			"span",
			"p", 
			"button",
			"legend", 
			"[label=", 
			"a",
			"h1", "h2", "h3", "h4", "h5", "h6"
		);
		
		foreach ( $translate as $t => $selector ) {
			$elements = $this->Find ($selector);
			
			// Loop through each tag and use the cLanguage function on them.
			foreach ( $elements as $e=> $element ) {
				
				$modified = __( ltrim ( rtrim ( $element->plaintext ) ) );
				
				// We're modifying the internal label, not the innertext
				if ( $element->label ) { 
					$elements[$e]->label = __( ltrim ( rtrim ( $element->label ) ) );
					continue;
				}
				
				// Check if any changes were made first, so that we can restore any lost HTML if no translation was provided.
				if ( $modified != ltrim ( rtrim ( $elements[$e]->plaintext ) ) ) {
					$elements[$e]->innertext = __( ltrim ( rtrim ( $element->plaintext ) ) );
				}
			}
		}
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
		
		foreach ( $pValues as $v => $value ) {
			if ( is_array ( $value ) ) {
				$option = sprintf ( "<optgroup label=\"%s\">", $v );
				foreach ( $value as $l => $label ) {
					$option .= sprintf ( '<option value="%s">%s</option>', $l, $label );
				}
				$option .= '</optgroup>';
				$this->Find($pSelector, 0)->innertext .= $option;
			} else { 
				$option = '<option value="%s">%s</option>';
				$this->Find($pSelector, 0)->innertext .= sprintf ( $option, $v, $value );
			}
			
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
		eval ( GLOBALS );
		
		// Throw in a reload for good measure.
		$this->Reload();
		
		$inputs = $this->Find("[name=]");
		
		// Set all request variable names to lower case
		foreach ( $zApp->GetSys ( "Request" )->Get() as $r => $request ) {
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
			
			// Remove the array reference from the name, and get the key array.
			if ( strstr ( $name, ']' ) ) {
				$result = preg_match ( '/\[(.*)\]/', $name, $nameKeys );
				$nameKey = $nameKeys[1];
				$name = preg_replace ( '/\[(.*)\]/', '', $name );
			}
			
			if ( isset ( $requests[$name] ) ) {
				if ( is_array ( $requests[$name] ) ) {
					$assign = $requests[$name][$nameKey];
				} else {
					$assign = $requests[$name];
				}
			} elseif ( isset ( $defaults[$name] ) ) {
				$assign = $defaults[$name];
			}
			
			if ( $assign ) {
				// Assign values from URL Request
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
					case 'button':
						continue;
					break;
					break;
					default:
						switch ( $type ) {
							case 'checkbox':
								$input->checked = $assign;
							break;
							case 'submit':
								continue;
							break;
							case 'password':
								continue;
							break;
							default:
								$input->value = $assign;
							break;
						}
					break;
				} 
			} else {
				// Special exception for checkboxes, since an unchecked box means no key/value pair was sent by the browser. 
				// @todo A similar check for radio boxes may be required.
				if ( $type == "checkbox" ) {
					$input->checked = null;
				}
			}
		}
		
		return ( true );
	}
	
	public function Link ( $pText, $pURL ) {
		
		$return = '<a href="' . $pURL . '">';
		
		$return .= $pText;
		
		$return .= '</a>';
		
		return ( $return );
	}
	
	public function DisableElement ( $pSelector ) {
		
		$element = $this->Find($pSelector, 0);
		
		$element->disabled = "disabled";
		
		return ( true );
		
	}
	
        
}

/** XML Class
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
