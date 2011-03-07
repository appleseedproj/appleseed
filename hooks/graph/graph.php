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

		$requestMethod = strtolower ( $this->GetSys ( "Request" )->Method() );

		$entry = $this->Get ( 'Config' )->GetConfiguration ( 'entry', '/graph/' );

		if ( !$entryData = $this->_EntryPoint() ) return ( false );

	    $Domain = $entryData['Domain'];	
	    $URI = $entryData['URI'];	

		$Parts = explode ( '/', $URI );

        $Request = Wob::_('Request');

        $Data = $Request->Get();

		$Signature = $this->GetSys ( 'Request' )->Get ( 'Signature' );

		// Split up the rest of the URI into "objects" 
		if ( count ( $Parts ) > 3 ) {
			for ( $p = 3; $p < count ( $Parts ); $p++ ) {
				$Parameters[] = $Parts[$p];
			}
			$PartCount = count ( $Parameters ) - 1;
			$last = $Parameters[$PartCount];
			$Parameters[$PartCount] = preg_replace ( '/\.xml$/', '', $Parts[$p-1] );
			$Parameters[$PartCount] = preg_replace ( '/\.json$/', '', $Parameters[$PartCount] );
			if ( !$Parameters[$PartCount] ) unset ( $Parameters[$PartCount] );
			$Data['objects'] = $Parameters;
		} else if ( count ( $Parts[2] ) ) {
			$last = $Parts[2];
			$Parts[2] = preg_replace ( '/\.xml$/', '', $Parts[2] );
			$Parts[2] = preg_replace ( '/\.json$/', '', $Parts[2] );
		} else if ( count ( $Parts[1] ) ) {
			$last = $Parts[1];
			$Parts[1] = preg_replace ( '/\.xml$/', '', $Parts[1] );
		}

		$sections = explode ( '.', $last );

		// Find the value past the . to determine the return format.
		$format = ltrim ( rtrim ( strtolower ( $sections[1] ) ) );

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

		$Component = ucwords ( $Parts[1] );
		$Method = ucwords ( $requestMethod ) . ucwords ( $Parts[2] );

		// 1.  Check if the method exists.
		if ( !$this->_ComponentMethodExists ( $Component, $Method ) ) {
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

	private function _EntryPoint ( ) {

		$request = $this->GetSys ( "Request" )->URI();

		$request = rtrim ( $request, '/' );

		$parts = explode ( '/', $request );

		$entry = $this->Get ( 'Config' )->GetConfiguration ( 'entry', '/graph/' );
		$version = $this->Get ( 'Config' )->GetConfiguration ( 'version', '0.1.0' );

		$protocol = 'ASDGRAPH/' . $version;

		$return = array();

		if ( $entry[0] == '/' ) {
			// This is a url redirect
			$entryPoint = ltrim ( rtrim ( $entry, '/' ), '/' );

			$pattern = '/^' . preg_quote ( $entryPoint , '/') . '/';
			$uri = preg_replace ( $pattern, '', $request );

			$return['Domain'] = ASD_DOMAIN;
			$return['URI'] = $uri;

			if ( $entryPoint == $request ) {
				// We're at the root, so return node information
				$this->_NodeInformation();
			} else if ( strpos ( $request, $entryPoint ) === 0 ) {
				// The request matches the entrypoint, so return URI/Domain
				return ( $return );
			}

			if ( $parts[0] == 'graph' ) {
				if ( count ( $parts ) == 1 ) {
					// Leave a default node root of graph/ for all sites
					$this->_NodeInformation();
				} else {
					// Can't access the graph from the wrong entrypoint.
					return ( false );
				}
			}
		} else {
			// This is a domain redirect.
			$entryParts = explode ( '/', $entry );
			$entryDomain = strtolower ( ltrim ( rtrim ( $entryParts[0] ) ) );

			unset ( $entryParts[0] );
			$entryPoint = ltrim ( rtrim ( join ('/', $entryParts ) , '/' ), '/' );

			$pattern = '/^' . preg_quote ( $entryPoint , '/') . '/';
			$uri = preg_replace ( $pattern, '', $request );

			$return['Domain'] = ASD_DOMAIN;
			$return['URI'] = $uri;

			// Check if we're on the proper domain
			if ( $entryDomain != ASD_DOMAIN ) {
				// Leave a default node root of graph/ for all sites
				if ( $parts[0] == 'graph' ) {
					$this->_NodeInformation();
				} else {
					// Can't access the graph from the wrong entrypoint.
					return ( false );
				} 
			} else {
				if ( $entryPoint == $request ) {
					// We're at the root, so return node information
					$this->_NodeInformation();
				} else if ( strpos ( $request, $entryPoint ) === 0 ) {
					// The request matches the entrypoint, so return URI/Domain
					return ( $return );
				} else if ( $request == 'graph' ) {
					// Leave a default node root of graph/ for all sites
					$this->_NodeInformation();
				}
			}
		}

		return ( false );

	}
	
	private function _NodeInformation ( ) {

		$entry = $this->Get ( 'Config' )->GetConfiguration ( 'entry', '/graph/' );
		$version = $this->Get ( 'Config' )->GetConfiguration ( 'version', '0.1.0' );

		$protocol = 'ASDGRAPH/' . $version;

		$result = array (
			'entry' => $entry,
			'version' => $protocol,
		);

		echo json_encode ( $result );
		exit;
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
