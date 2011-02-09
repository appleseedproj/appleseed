<?php
/**
 * @version      $Id$
 * @package      Appleseed.Framework
 * @subpackage   Graph
 * @copyright    Copyright (C) 2004 - 2011 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Graph Hook Class
 * 
 * Graph Hook Class
 * 
 * @package     Appleseed.Framework
 * @subpackage  System
 */
class cGraphHook extends cHook {

	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	/*
	 * Trap the Graph API entry point.
	 * 
	 */
	public function EndSystemInitialize ( $pData = null ) {

		$request = $this->GetSys ( "Request" )->URI();
		$requestMethod = strtolower ( $this->GetSys ( "Request" )->Method() );

		$parts = split ( '/', $request );

		if ( $parts[0] != 'graph' ) return ( false );

		$Component = ucwords ( $parts[1] );
		$Method = ucwords ( $requestMethod ) . ucwords ( $parts[2] );
		$Parameters = split ( '/', $parts[3] );

		$Signature = $this->GetSys ( 'Request' )->Get ( 'Signature' );

		// Find the last parameter in the list.
		$length = count ( $Parameters );
		$sections = explode ( '.', $Parameters[$length-1] );

		// Find the value past the . to determine the return format.
		$last = count ( $sections ) - 1;
		$format = ltrim ( rtrim ( strtolower ( $sections[$last] ) ) );

		// Currently supported is XML and JSON format.
		switch ( $format ) {
			case 'xml':
			case 'json':
			break;
			default:
				// Default to JSON
				$format = 'json';
			break;
		}

		// 1.  Check if the method exists.
		if ( !$this->_ComponentMethodExists ( $Component, $Method, $Parameters ) ) {
			// We cannot resolve to a component, error out.
			$this->_Error ( '404' );
			exit;
		}

		// 2. Determine proper authorization.
		if ( !$this->_CheckAccess ( $Component, $Method ) ) {
			// No access
			$this->_Error ( '403' );
			exit;
		}

		// 3. Execute the component method.
		$return = $this->GetSys ( 'Components' )->Talk ( $Component, $Method, $Data );

		// 4. Translate the resulting value.
		switch ( $format ) {
			case 'xml':
				header ("content-type: text/xml; charset=utf-8"); 

				$output = $this->_ArrayToXML ( "<result/>", $return );
			break;
			case 'json':
			default:
				header('content-type: application/json; charset=utf-8');

				$output = json_encode ( $return );
			break;
		}

		echo $output;

		// Exit the framework completely
		exit;
	}

	private function _CheckAccess ( $pComponent, $pMethod, $pParameters ) {
		return ( true );
	}

	private function _ComponentMethodExists ( $pComponent, $pMethod, $pRequestMethod, $pParameters ) {

		$Components =  $this->GetSys ( 'Components' )->Get ( 'Config' )->Get ( 'Components' );
		$Component = strtolower ( $pComponent );

		// Return false if component isn't found.
		if ( !in_array ( $Component, $Components ) ) return ( false );

		$Component = $this->GetSys ('Components' )->$pComponent;

		if ( !method_exists ( $Component, $pMethod ) ) return ( false );

		return ( true );
	}

	private function _Error ( $pError ) {
		switch ( $pError ) {
			case '404':
				header("HTTP/1.1 404 Not Found");
			break;
			case '403':
				header("HTTP/1.1 403 Not Found");
			break;
		}

		return ( true );
	}

	/*
	 * Adapted from php.net
	 * Original author: phil at dier dot us
	 *
	 * @todo:  Replace with a better approach.
	*/
	private function _ArrayToXML ( $pRoot, $pArray ) { 
    	$xml = new SimpleXMLElement($pRoot); 
    	$f = create_function('$f,$c,$a',' 
            foreach($a as $k=>$v) { 
                if(is_array($v)) { 
                    $ch=$c->addChild($k); 
                    $f($f,$ch,$v); 
                } else { 
					if ( is_int ($k) ) $k = "value";
                    $c->addChild($k,$v); 
                } 
            }'); 
    	$f ( $f, $xml, $pArray); 

		$dom = new DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($xml->asXML());

		return ( $dom->saveXML() );
	} 
}
