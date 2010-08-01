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

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
	}
	
	public function Email ( $pValue ) {
	}

	public function Url ( $pValue ) {
	}

	public function Username ( $pValue ) {
	}

	public function Domain ( $pValue ) {
	}

	public function Null ( $pValue ) {
	}

	public function NotNull ( $pValue ) {
	}

	public function Digits ( $pValue ) {
	}

	public function Number ( $pValue ) {
	}

	public function Required ( $pValue, $pCharacters ) {
	}

	public function Illegal ( $pValue, $pCharacters ) {
	}

	public function Length ( $pValue, $pMin, $pMax ) {
	}

	public function MinLength ( $pValue, $pMin ) {
	}

	public function MaxLength ( $pValue, $pMax ) {
	}

	public function Size ( $pValue, $pMin, $pMax ) {
	}

	public function MinSize ( $pValue, $pMin ) {
	}
	
	public function MaxSize ( $pValue, $pMax ) {
	}
	
}