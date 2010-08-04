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

/** Validation Class
 * 
 * Validation management
 * 
 * @package     Appleseed.Framework
 * @subpackage  Library
 */
class cValidation {
	
	protected $_Reasons = array ();

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
	}
	
	public function GetReasons ( ) {
		
		return ( $this->_Reasons );
	}
	
	public function Email ( $pValue ) {
		$result = filter_var ( $pValue , FILTER_VALIDATE_EMAIL );
		
		if ( $result ) return ( true );
		
		return ( false );
	}

	public function Url ( $pValue ) {
		$result = filter_var ( $pValue , FILTER_VALIDATE_URL );
		
		if ( $result ) return ( true );
		
		return ( false );
	}

	public function Username ( $pValue ) {
		
		$illegal = '/ * \ < > ( ) [ ] & ^ $ # @ ! ? ; \' " { } | ~ + = %20';
		
		if ( !$this->Required ( $pValue, $illegal ) ) return ( false ); 
		
		if ( !$this->MinLength ( $pValue, 6 ) ) return ( false ); 
		
		if ( !$this->MinLength ( $pValue, 16 ) ) return ( false ); 
		
		return ( true );
	}

	public function Domain ( $pValue ) {
	}

	public function Null ( $pValue ) {
		if ( !$pValue ) return ( true );
		
		return ( false );
	}

	public function NotNull ( $pValue ) {
		if ( $pValue ) return ( true );
		
		return ( false );
	}

	public function Digits ( $pValue ) {
	}

	public function Number ( $pValue ) {
	}

	public function Required ( $pValue, $pCharacters ) {
		
		$characters = explode ( ' ', $pCharacters );

		foreach ($characters as $c) {
			
			if ($c == "%20") $c = " ";

				if (!strpos ($pValue, $c)) {
					return ( false );
				} // if
			} 
        
        return ( true );
	}

	public function Illegal ( $pValue, $pCharacters ) {
		
		$characters = explode ( ' ', $pCharacters );

		foreach ($characters as $c) {
			
			if ($c == "%20") $c = " ";

				if (strpos ($pValue, $c)) {
					return ( false );
				} // if
			} 
        
        return ( true );
	}

	public function Length ( $pValue, $pMin, $pMax ) {
		
		if ( ( strlen ( $pValue ) >= $pMin ) && ( strlen ( $pValue ) <= $pMax ) ) return ( true );
		
		return ( false );
	}

	public function MinLength ( $pValue, $pMin ) {
		
		if ( ( strlen ( $pValue ) >= $pMin ) ) return ( true );
		
		return ( false );
	}

	public function MaxLength ( $pValue, $pMax ) {
		
		if ( ( strlen ( $pValue ) <= $pMax ) ) return ( true );
		
		return ( false );
	}

	public function Size ( $pValue, $pMin, $pMax ) {
		
		if ( ( $pValue >= $pMin ) && ( $pValue <= $pMax ) ) return ( true );
		
		return ( false );
	}

	public function MinSize ( $pValue, $pMin ) {
		
		if ( ( $pValue >= $pMin ) ) return ( true );
		
		return ( false );
	}
	
	public function MaxSize ( $pValue, $pMax ) {
		
		if ( ( $pValue <= $pMax ) ) return ( true );
		
		return ( false );
	}
	
}