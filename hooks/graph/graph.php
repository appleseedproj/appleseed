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

	private $_Component;
	private $_Method;
	private $_Format;

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
			$Data = $Parameters;
		} else if ( $Parts[2] ) {
			$last = $Parts[2];
			$Parts[2] = preg_replace ( '/\.xml$/', '', $Parts[2] );
			$Parts[2] = preg_replace ( '/\.json$/', '', $Parts[2] );
		} else if ( $Parts[1] ) {
			$last = $Parts[1];
			$Parts[1] = preg_replace ( '/\.xml$/', '', $Parts[1] );
			$Parts[1] = preg_replace ( '/\.json$/', '', $Parts[1] );
		}

		$sections = explode ( '.', $last );

		// Find the value past the . to determine the return format.
		$this->_Format = ltrim ( rtrim ( strtolower ( $sections[1] ) ) );

		// Currently supported is XML and JSON format.
		switch ( $this->_Format ) {
			case 'xml':
			case 'json':
			break;
			default:
				// Default to JSON
				$this->_Format = 'json';
			break;
		}

		$this->_Component = ucwords ( $Parts[1] );
		$this->_Method = ucwords ( $requestMethod ) . ucwords ( $Parts[2] );

		// 1.  Check if the method exists.
		if ( ( !$this->_ComponentMethodExists ( $this->_Component, $this->_Method ) ) && ( $requestMethod != 'options' ) ) {
			// We cannot resolve to a component, error out.
			$this->_Error ( '404' );
			exit;
		}

		// 2. Determine proper authorization.
		if ( !$this->_CheckAccess ( $this->_Component, $this->_Method ) ) {
			// No access
			$this->_Error ( '403' );
			exit;
		}

		$c = $this->_Component;
		$instance = $this->GetSys ( 'Components' )->$c;
		$m = $this->_Method;

		$reflect = new ReflectionClass ( $instance );
		$className = $reflect->GetName();

		if ( $method = $reflect->hasMethod ( $m ) ) {
			$method = $reflect->getMethod ( $m );

			$parameters = $method->getParameters();
	
			if ( $method->GetDeclaringClass()->getName() != $className ) {
				$this->_Error ( '403' );
				exit;
			}
		} else {
			$method = null;
			$parameters = array ();
		}

		// 3. Align the parameters.
		$RequestData = $Request->Get();
		$Params = $Data;
		$pnames = array();
		foreach ( $parameters as $p => $parameter ) {
			$pname = $parameter->GetName();
			if ( ( $pname[0] == 'p' ) && ( ctype_upper ( $pname[1] ) ) ) {
				// We're using $pParameter notation.  Remove the p.
				list ( $null, $name ) = explode ( $pname[0], $pname, 2 );
				$name = strtolower ( $name );
				if ( $RequestData[$name] ) {
					if ( !isset ( $Parameters[$p] ) ) 
						$Parameters[$p] = $RequestData[$name];
				}
			} else {
				// We're not using $pParameter notation
				$name = strtolower ( $pname );
				$Parameters[$p] = $RequestData[$name];
			}
			$pnames[$p] = $name;

		}

		// Check to make sure all necessary parameters are available.
		$return = array();
		foreach ( $parameters as $p => $parameter ) {
			if ( ( !$parameter->IsOptional() ) && ( !isset ( $Parameters[$p] ) ) ) {
				header('HTTP/1.1 412 Precondition Failed');
				$return['error'] = true;
				$return['message'][] = 'Field required: "' . $pnames[$p] . '"';
			}
		}

		// If we've found an error, exit.
		if ( $return['error'] == true ) {
			$this->Format ( $return );
			exit;
		}

		$this->_Parameters = (array) $Parameters;

		// Requesting information on methods
		if ( $requestMethod == 'options' ) {
			if ( $Parts[2] ) {
				$this->_Options ( ucwords ( $Parts[2] ) );
			} else {
				$this->_OptionsAll ( );
			}
		}

		// 4. Execute the component method.
        $return = call_user_func_array ( array ( $instance, $this->_Method ), $this->_Parameters  );

		$this->Format ( $return );

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
				header("HTTP/1.1 403 Forbidden");
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

	private function _Options ( $pObject ) {

		// 1.  Check if the method exists.
		if ( $this->_ComponentMethodExists ( $this->_Component, 'Options' . $pObject ) ) {
			// Override Options
			$c = $this->_Component;
			$instance = $this->GetSys ( 'Components' )->$c;
        	$return = call_user_func_array ( array ( $instance, 'Options' ), $this->_Parameters  );
			$this->Format ( $return );
			exit;
		}

		$return = array();
		$return['interface'] = strtolower ( $this->_Component );
		$return['object'] = strtolower ( $pObject );
		$return['methods'] = array();

		$class = $this->_Component;
		$reflect = new ReflectionClass ( $this->GetSys ( 'Components' )->$class );
		$methods = $reflect->getMethods();

		$className = $reflect->GetName();

		foreach ( $methods as $m => $method ) {

			// Skip if we're looking at an inherited method.
			if ( $method->GetDeclaringClass()->getName() != $className ) continue;

			$methodType = null;

			switch ( $method->getName() ) {
				case 'Get' . $pObject;
					$methodType = 'get';
				break;
				case 'Post' . $pObject;
					$methodType = 'post';
				break;
				case 'Put' . $pObject;
					$methodType = 'put';
				break;
				case 'Delete' . $pObject;
					$methodType = 'delete';
				break;
				default:
				break;
			}

			if ( !$methodType ) continue;

			$parameters = $method->getParameters();
			foreach ( $parameters as $p => $parameter ) {
				$name = strtolower ( $parameter->getName() );
				$return['methods'][$methodType]['params'][] = array ();
				$pointer = count ( $return['methods'][$methodType]['params'] ) - 1;
				$return['methods'][$methodType]['params'][$pointer]['name'] = $name;
				if ( $parameter->isDefaultValueAvailable() ) {
					$return['methods'][$methodType]['params'][$pointer]['required'] = '0';
					$return['methods'][$methodType]['params'][$pointer]['default'] = $parameter->getDefaultValue();
				} else {
					$return['methods'][$methodType]['params'][$pointer]['required'] = '1';
				}
			}
		}

		$this->Format ( $return );
		exit;
	}

	private function _OptionsAll ( ) {

		// 1.  Check if the method exists.
		if ( $this->_ComponentMethodExists ( $this->_Component, 'Options' ) ) {
			// Override Options
			$c = $this->_Component;
			$instance = $this->GetSys ( 'Components' )->$c;
        	$return = call_user_func_array ( array ( $instance, 'Options' ), $this->_Parameters  );
			$this->Format ( $return );
			exit;
		}

		$return = array();
		$return['interface'] = strtolower ( $this->_Component );
		$return['objects'] = array();

		$class = $this->_Component;
		$reflect = new ReflectionClass ( $this->GetSys ( 'Components' )->$class );
		$methods = $reflect->getMethods();

		$className = $reflect->GetName();

		foreach ( $methods as $m => $method ) {

			// Skip if we're looking at an inherited method.
			if ( $method->GetDeclaringClass()->getName() != $className ) continue;

			// Only match REST request methods.
			if ( !preg_match ( "/^(Get|Post|Put|Delete)(.*)/", $method->getName(), $matches) ) {
				continue;
			}

			$methodType = strtolower ( $matches[1] );
			$object = strtolower ( $matches[2] );

			$return['objects'][$object]['methods'][$methodType] = array();

			$parameters = $method->getParameters();
			foreach ( $parameters as $p => $parameter ) {
				$name = strtolower ( $parameter->getName() );
				$return['objects'][$object]['methods'][$methodType]['params'][] = array ();
				$pointer = count ( $return['objects'][$object]['methods'][$methodType]['params'] ) - 1;
				$return['objects'][$object]['methods'][$methodType]['params'][$pointer]['name'] = $name;
				if ( $parameter->isDefaultValueAvailable() ) {
					$return['objects'][$object]['methods'][$methodType]['params'][$pointer]['required'] = '0';
					$return['objects'][$object]['methods'][$methodType]['params'][$pointer]['default'] = $parameter->getDefaultValue();
				} else {
					$return['objects'][$object]['methods'][$methodType]['params'][$pointer]['required'] = '1';
				}
			}
		}

		$this->Format ( $return );
		exit;
	}

	private function Format ( $pResponse ) {

		switch ( $this->_Format ) {
			case 'xml':
				header ("content-type: text/xml; charset=utf-8"); 

				$output = xml_encode ( $pResponse );
			break;
			case 'json':
			default:
				header('content-type: application/json; charset=utf-8');

				$output = json_encode ( $pResponse );
			break;
		}

		echo $output;

		return ( true );
	}
}
