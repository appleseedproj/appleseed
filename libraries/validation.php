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
	
	public function ClearReasons ( ) {
		
		$this->_Reasons = array();
		
		return ( true );
	}
	
	public function Validate ( $pFields, $pData ) {
		
		foreach ( $pData as $d => $data) {
			$variables[] = $d;
		}
		
		foreach ( $pFields as $f => $field ) {
			$fieldName = $field['Field'];
			$fieldNameLower = strtolower ( ltrim ( rtrim ( $fieldName ) ) );
			if ( !in_array ( $fieldNameLower, $variables ) ) continue;
			
			$validate[$fieldName] = $field;
		}
		
		$return = true;
		
		foreach ( $validate as $v => $valid ) {
			$fieldNameLower = ltrim ( rtrim ( strtolower ( $v ) ) );
			$type = $valid['Type'];
			$null = $valid['Null'];
			$extra = $valid['Extra'];
			
			preg_match ( '/(.*)\((.*)\)/', $type, $info );
			$type = $info[1];
			$storageSize = $info[2];
			
			/*
			 * tinyint		1 bytes	-128 to 127									0 to 255
			 * smallint		2 bytes	-32768 to 32767								0 to 65535
			 * mediumint	3 bytes	-8388608 to 8388607							0 to 16777215
			 * int			4 bytes	-2147483648 to 2147483647					0 to 4294967295
			 * bigint		8 bytes	-9223372036854775808 to 9223372036854775807	0 to 18446744073709551615
			 */
			
			$value = $pData[$fieldNameLower];
			
			if ( $null == 'NO' ) {
				if ( !preg_match ( "/auto_increment/", $extra ) ) {
					if ( ! $this->NotNull ( $value ) ) {
						$return = false;
						$this->_Reasons[$v][] = "Cannot Be Null";
					}
				}
			}
			
			switch ( $type ) {
				case 'varchar':
				case 'char':
					$maxlength = $storageSize;
					if ( ! $this->MaxLength ( $value, $maxlength ) ) {
						$return = false;
						$this->_Reasons[$v][] = "Too Long";
					}
				break;
				case 'tinyint':
					$minSize = -128;
					$maxSize = 127;
					if ( ! $this->MinSize ( $value, $minSize ) ) {
						$return = false;
						$this->_Reasons[$v][] = "Too Small";
					}
					if ( ! $this->MaxSize ( $value, $maxSize ) ) {
						$return = false;
						$this->_Reasons[$v][] = "Too Large";
					}
				break;
				case 'smallint':
					$minSize = -32768;
					$maxSize = 32767;
					if ( ! $this->MinSize ( $value, $minSize ) ) {
						$return = false;
						$this->_Reasons[$v][] = "Too Small";
					}
					if ( ! $this->MaxSize ( $value, $maxSize ) ) {
						$return = false;
						$this->_Reasons[$v][] = "Too Large";
					}
				break;
				case 'mediumint':
					$minSize = -8388608;
					$maxSize = 8388607;
					if ( ! $this->MinSize ( $value, $minSize ) ) {
						$return = false;
						$this->_Reasons[$v][] = "Too Small";
					}
					if ( ! $this->MaxSize ( $value, $maxSize ) ) {
						$return = false;
						$this->_Reasons[$v][] = "Too Large";
					}
				break;
				case 'int':
					$minSize = -2147483648;
					$maxSize = 2147483647;
					if ( ! $this->MinSize ( $value, $minSize ) ) {
						$return = false;
						$this->_Reasons[$v][] = "Too Small";
					}
					if ( ! $this->MaxSize ( $value, $maxSize ) ) {
						$return = false;
						$this->_Reasons[$v][] = "Too Large";
					}
				break;
				case 'bigint':
					$minSize = -9223372036854775808;
					$maxSize = 9223372036854775807;
					if ( ! $this->MinSize ( $value, $minSize ) ) {
						$return = false;
						$this->_Reasons[$v][] = "Too Small";
					}
					if ( ! $this->MaxSize ( $value, $maxSize ) ) {
						$return = false;
						$this->_Reasons[$v][] = "Too Large";
					}
				break;
			}
			
		}
		
		return ( $return );
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