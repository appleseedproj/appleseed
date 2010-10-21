<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Wall
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Page Component
 * 
 * Page Component Entry Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Page
 */
class cPage extends cComponent {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct();
	}
	
	public function AddToProfileTabs ( $pData = null ) {
		
		$return = array ();
		
		$return[] = array ( 'id' => 'page', 'title' => 'Page Tab', 'link' => '/page/' );
		
		return ( $return );
	} 
	
	public function IdentifierExists ( $pData = null ) {
		
		$Identifier = $pData['Identifier'];
		
		$Model = new cModel ( 'PageReferences' );
		
		$Model->Retrieve ( array ( 'Identifier' => $Identifier ) );
		
		if ( $Model->Get ( 'Total' ) > 0 ) {
			return ( true );
		}
		
		return ( false );
	}
	
	public function Status ( $pData = null ) {
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		include ( ASD_PATH . 'components/page/models/page.php' );
		$Page = new cPageModel ();
		
		include ( ASD_PATH . 'components/page/models/references.php' );
		$References = new cPageReferencesModel ();
		
		$Page->RetrieveCurrent( $this->_Focus->Id );
		if ( $Page->Get ( "Total" ) == 0 ) return ( false );
		
		$Page->Fetch();
		
		$References->Retrieve ( array ( "Identifier" => $Page->Get ( "Identifier" ) ) );
		if ( $References->Get ( "Total" ) == 0 ) return ( false );
		
		$References->Fetch();
		
		$return = array();
		
		$Identifier = $References->Get ( 'Identifier' );
		$Access = $this->Talk ( 'Privacy', 'Check', array ( 'Type' => 'Post', 'Identifier' => $Identifier ) );
		
		$return['Content'] = $Page->Get ( 'Content' );
		$return['Stamp'] = $References->Get ( 'Stamp' );
		
		if ( $Access ) {
			return ( $return );
		} else {
			// If the person viewing is the focus user, grant access
			if ( $this->_Focus->Account == $this->_Current->Account ) {
				return ( $return );
			} else {
				return ( false );
			}
		}
		
		return ( false );
	}
	
	public function ClearStatus ( $pData = null ) {
		
		$user = $pData['UserId'];
		
		include ( ASD_PATH . 'components/page/models/page.php' );
		$Page = new cPageModel ();
		
		$Page->ClearCurrent ( $user );
		
		return ( true );
	}
	
	public function RegisterPageType ( $pData = null ) {
		
		$post = new stdClass();
		$post->Component = $this->Get ( 'Component' );
		$post->Function = 'GetPost';
		
		$return = array ( 'Post' => $post );
		
		return ( $return );
	}
	
	public function GetPost ( $pData = null ) {
		
		$this->_Current = $this->Talk ( 'User', 'Current' );
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		
		$Identifier = $pData['Identifier'];
		$Account = $pData['Account'];
		
		// Check the privacy settings on this item.
		$Access = $this->Talk ( 'Privacy', 'Check', array ( 'Type' => 'Post', 'Identifier' => $Identifier ) );
		
		// Load the Post data
		include_once ( ASD_PATH . 'components/page/models/page.php' );
		$Model = new cPageModel();
		
		$Post = $Model->RetrievePost ( $this->_Focus->Id, $Identifier );
		
		$return['Owner'] = $Post['Owner'];
		$return['Comment'] = $Post['Content'];
		
		// If true, then return the post.
		if ( $Access ) {
			return ( $return );
		} else {
			// If the person viewing is the owner, grant access.
			if ( $this->_Current->Account == $Post['Owner'] ) {
				return ( $return );
			} else if ( $this->_Focus->Account == $this->_Current->Account ) {
				return ( $return );
			} else {
				return ( false );
			}
		}
		
		return ( false );
	}
	
}
