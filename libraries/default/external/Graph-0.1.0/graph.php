<?php
/**
 * @version      $Id$
 * @package      Appleseed Social Graph
 * @copyright    Copyright (C) 2011 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org/graph/
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEEDSOCIALGRAPH' ) or die( 'Direct Access Denied' );

/** Graph Class
 * 
 * Appleseed Social Graph class
 * 
 * @package     Appleseed.Framework
 * @subpackage  Library
 */
class cAppleseedSocialGraph {

	function __construct ( ) {
	}

	/*
     * Load an existing token, or create and store a new one.
     */
	function Token ( $pIdentity, $pOrigin, $pDestination, $pSecret, $pDuration, $fSaveToken, $fLoadToken ) {

		// Duration is in minutes, multiply by 60 and  add to current time.
		$Expiration = time() + ( $pDuration * 60 );

		$String = $pIdentity . $pOrigin . $pDestination . $Expiration;
		$Token = hash_hmac ( 'sha512', $String, $pSecret );

		$Expiration = $this->_FormatDate ( $Expiration );

		$return = array ( $Token, $Expiration );

		return ( $return );
	}

	/*
	 *  Take a GMT UNIX timestamp and format it into required format.
	 *
	 *  YYYY-MM-DDTHH:MM:SSZ
	 *
	 */
	private function _FormatDate ( $pStamp ) {

        $difference = date ( 'O', $pStamp );

        $year = date ( 'Y', $pStamp );
        $month = date ( 'm', $pStamp );
        $day = date ( 'd', $pStamp );

        $return = $year . '-' . $month . '-' . $day . 'T';

        $hour = date ( 'H', $pStamp );
        $minute = date ( 'i', $pStamp );
        $second = date ( 's', $pStamp );

        $return .= $hour . ':' . $minute . ':' . $second . 'Z';

        return ( $return );
    }

}

?>
