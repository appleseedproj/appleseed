<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Photos
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Photos Component Sets Controller
 * 
 * Photos Component Sets Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Photos
 */
class cPhotosSetsController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {

		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		$this->View = $this->GetView ( $pView ); 
		
		$this->Sets = $this->GetModel ( 'Sets' );

		$this->Photo = $this->GetModel ( 'Photos' );
		
		$this->Sets->Load ( $this->_Focus->Id );

		$this->_Prep();
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _Prep ( ) {

		if ( $this->_Focus->Account == $this->_Current->Account ) {
			$this->View->Find ( 'form[class="add"]', 0 )->action = '/profile/' . $this->_Focus->Username . '/photos/';
			$this->View->Find ( '[name="Context"]', 0 )->value = $this->Get ( 'Context' );
		} else {
			$this->View->Find ( 'form[class="add"]', 0 )->outertext = '';
		}

		$list = $this->View->Find ( '.list', 0);

		$itemOriginal = $this->View->Find ( '.item', 0 )->outertext;

		$list->innertext = "";

		if ( $this->Sets->Get ( 'Total' ) == 0 ) return ( false );
		
		while ( $this->Sets->Fetch() ) {
            $item = new cHTML ();
            $item->Load ( $itemOriginal );

			$id = $this->Sets->Get ( 'Set_PK' );

			$this->Photo->GetCover ( $id );
			$item->Find ( '.name', 0 )->innertext = $this->Sets->Get ( 'Name' );
			$item->Find ( '.description', 0 )->innertext = $this->Sets->Get ( 'Description' );

			$filename = $this->Photo->Get ( 'Filename' );
			$extension = pathinfo ( $filename, PATHINFO_EXTENSION );

			$link = 'http://' . ASD_DOMAIN . '/profile/' . $this->_Focus->Username . '/photos/' . $this->Sets->Get ( 'Directory' );
			$item->Find ( '.photoset-name-link', 0 )->href = $link;
			$item->Find ( '.photoset-cover-link', 0 )->href = $link;

			list ( $file ) = explode ( '.' . $extension, $filename );

			$Identifier = $this->Photo->Get ( 'Identifier' );

			$coverLocation = 'http://' . ASD_DOMAIN . '/_storage/photos/' . $this->_Focus->Username . '/' . $this->Sets->Get ( 'Directory' ) . '/' . $Identifier . '.m' . '.jpg';
			$coverFile = ASD_PATH . '_storage/photos/' . $this->_Focus->Username . '/' . $this->Sets->Get ( 'Directory' ) . '/' . $Identifier . '.m' . '.jpg';
			if ( file_exists ( $coverFile ) ) {
				$item->Find ( '.cover', 0 )->src = $coverLocation;
			} else {
				$item->Find ( '.cover', 0 )->class .= ' none ';
			}
			$list->innertext .= $item->outertext;
		}

		return ( true );
	}

	public function Add ( $pView = null, $pData = array ( ) ) {
		
		// Determine access.
		if ( !$this->_CheckAccess ( ) ) {
			$this->GetSys ( 'Foundation' )->Redirect ( 'common/403.php' );
			return ( false );
		}

		$this->View = $this->GetView ( 'sets.edit' ); 
		
		$this->Sets = $this->GetModel ( 'Sets' );
		$this->Photos = $this->GetModel ( 'Photos' );

		$this->Sets->Load ( $this->_Focus->Id );

		$this->Photos->Load ( $Identifier );
		$this->Photos->Fetch();

		$this->_PrepAdd();
		
		$this->View->Synchronize();
		
		$this->View->Display();
		
		return ( true );
	}

	public function Edit ( $pView = null, $pData = array ( ) ) {
		
		// Determine access.
		if ( !$this->_CheckAccess ( ) ) {
			$this->GetSys ( 'Foundation' )->Redirect ( 'common/403.php' );
			return ( false );
		}

		$this->View = $this->GetView ( 'sets.edit' ); 
		
		$this->Sets = $this->GetModel ( 'Sets' );
		$this->Photos = $this->GetModel ( 'Photos' );

		$Set = (int) $this->GetSys ( 'Request' )->Get ( 'Set' );
		$this->Sets->Load ( $this->_Focus->Id, $Set );

		if ( $this->Sets->Get ( 'Total' ) == 0 ) {
			$this->GetSys ( 'Foundation' )->Redirect ( 'common/404.php' );
			return ( false );
		}

		$data = $this->Sets->Get ( 'Data' );

		$this->Photos->Load ( $Identifier );
		$this->Photos->Fetch();

		$this->_PrepEdit();
		
		$this->View->Synchronize( $data );
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _PrepAdd ( ) {

		$this->View->Find ( '[name="Context"]', 0 )->value = $this->Get ( 'Context' );
			
		$privacyData = array ( 'Type' => 'photosets', 'Identifier'  => $Identifier );
		$privacyControls =  $this->View->Find ('.privacy');
		foreach ( $privacyControls as $c => $control ) {
			$control->innertext = $this->GetSys ( 'Components' )->Buffer ( 'privacy', $privacyData );
		}

		$this->_PrepMessage();

		return ( true );
	}

	private function _PrepEdit ( ) {

		$this->View->Find ( '[name="Context"]', 0 )->value = $this->Get ( 'Context' );
		$this->View->Find ( '[name="Set_PK"]', 0 )->value = $this->Sets->Get ( 'Set_PK' );

		$this->View->Find ( '.photos-edit-title', 0 )->innertext = 'Edit Set Title';
		$this->View->Find ( '.add-edit', 0 )->innertext = 'Edit Set';
			
		$privacyData = array ( 'Type' => 'photosets', 'Identifier'  => $this->Sets->Get ( 'Identifier' ) );
		$privacyControls =  $this->View->Find ('.privacy');
		foreach ( $privacyControls as $c => $control ) {
			$control->innertext = $this->GetSys ( 'Components' )->Buffer ( 'privacy', $privacyData );
		}

		$this->_PrepMessage();

		return ( true );
	}

	public function Save ( ) {

		$Request = $this->GetSys ( 'Request' );

		$this->Sets = $this->GetModel ( 'Sets' );
		$this->Photos = $this->GetModel ( 'Photos' );

		// 0. Determine access.
		if ( !$this->_CheckAccess ( ) ) {
			$this->GetSys ( 'Foundation' )->Redirect ( 'common/403.php' );
			return ( false );
		}

		$Set = $Request->Get ( 'Set' );

		if ( $Set ) {
			return ( $this->_Update ( ) );
		}

		// 1. Sanity check all the inputs.
		$Directory = $Request->Get ( 'Directory' );

		$this->Sets->Load ( $this->_Focus->Id, $Directory );

		if ( !$this->_Sanity() ) {
			return ( $this->Add() );
		}

		// 2. Create an album if necessary.
		$Location = $this->_CreateDirectory ( $this->_Focus->Username, $Directory );

		// @todo: Move to model
		$Name = $Request->Get ( 'Name' );
		$Identifier = $this->CreateUniqueIdentifier();
		$Description = $Request->Get ( 'Description' );

		$this->Sets->Destroy ( 'Set_PK' );
		$this->Sets->Set ( 'Identifier', $Identifier );
		$this->Sets->Set ( 'Owner_FK', $this->_Focus->Id );
		$this->Sets->Set ( 'Name', $Name );
		$this->Sets->Set ( 'Description', $Description );
		$this->Sets->Set ( 'Directory', $Directory );
		$this->Sets->Set ( 'Created', NOW() );
		$this->Sets->Set ( 'Updated', NOW() );
		$this->Sets->Save();

		$Privacy = $Request->Get ( 'Privacy' );

       	$privacyData = array ( 'Privacy' => $Privacy, 'Type' => 'Photosets', 'Identifier' => $Identifier );
       	$this->GetSys ( 'Components' )->Talk ( 'Privacy', 'Store', $privacyData );

		// 6. Redirect to the new photo.
		$Redirect = 'http://' . ASD_DOMAIN . '/profile/' . $this->_Focus->Username . '/photos/' . $Directory . '/';

        header ( "Location:" . $Redirect );
		exit;
	}

	private function _Update ( ) {

		if ( !$this->_Sanity() ) {
			return ( $this->Edit() );
		}

		$Request = $this->GetSys ( 'Request' );

		$this->Sets = $this->GetModel ( 'Sets' );
		$this->Photos = $this->GetModel ( 'Photos' );

		$Set = (int)$Request->Get ( 'Set' );

		$Directory = $Request->Get ( 'Directory' );

		$this->Sets->Load ( $this->_Focus->Id, $Set );

		if ( $this->Sets->Get ( 'Total' ) == 0 ) {
			$this->GetSys ( 'Foundation' )->Redirect ( 'common/404.php' );
			return ( false );
		}

		$NewDirectory = strtolower ( ltrim ( rtrim ( $Directory ) ) );
		$OldDirectory = strtolower ( ltrim ( rtrim ( $this->Sets->Get ( 'Directory' ) ) ) );

		// 2. Rename the directory.
		if ( $NewDirectory != $OldDirectory ) {
			if ( !$this->_RenameDirectory ( $OldDirectory, $NewDirectory ) ) {
				$this->GetSys ( 'Session' )->Context ( $this->Get ( 'Context' ) );         
				$this->GetSys ( 'Session' )->Set ( 'Message', __( 'Cannot Move Directory', array ( 'old' => $OldDirectory, 'new' => $NewDirectory ) ) );
				$this->GetSys ( 'Session' )->Set ( 'Error', true );
				return ( $this->Edit ( ) );
			}
		}

		// @todo: Move to model
		$Name = $Request->Get ( 'Name' );
		$Identifier = $this->Sets->Get ( 'Identifier' );
		$Description = $Request->Get ( 'Description' );

		$this->Sets->Set ( 'Set_PK', $Set );
		$this->Sets->Protect ( 'Identifier' );
		$this->Sets->Protect ( 'Owner_FK' );
		$this->Sets->Set ( 'Name', $Name );
		$this->Sets->Set ( 'Description', $Description );
		$this->Sets->Set ( 'Directory', $Directory );
		$this->Sets->Protect ( 'Created' );
		$this->Sets->Set ( 'Updated', NOW() );
		$this->Sets->Save();

		$Privacy = $Request->Get ( 'Privacy' );

       	$privacyData = array ( 'Privacy' => $Privacy, 'Type' => 'Photosets', 'Identifier' => $Identifier );
       	$this->GetSys ( 'Components' )->Talk ( 'Privacy', 'Store', $privacyData );

		// 6. Redirect to the new photo.
		$Redirect = 'http://' . ASD_DOMAIN . '/profile/' . $this->_Focus->Username . '/photos/' . $Directory . '/';

        header ( "Location:" . $Redirect );
		exit;

		return ( true );
	}

	private function _CheckAccess ( ) {

		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );

		if ( ( $this->_Focus->Account != $this->_Current->Account ) ) {
			return ( false );
		}   
			
		return ( true );
	}

	private function _Sanity ( ) {

		$this->GetSys ( 'Session' )->Context ( $this->Get ( 'Context' ) );         

		$Validate = $this->GetSys ( 'Validation' );
		$Request = $this->GetSys ( 'Request' );

		$Set = $Request->Get ( 'Set' );
		$Name = $Request->Get ( 'Name' );
		$Directory = $Request->Get ( 'Directory' );
		$Description = $Request->Get ( 'Description' );

		$fields = $this->Sets->Get ( 'Fields' );
		$data = $Request->Get();

		if ( !isset ( $Directory ) ) {
			$this->GetSys ( 'Session' )->Set ( 'Message', __( 'Cannot Be Null', array ( 'field' => 'Directory' ) ) );
			$this->GetSys ( 'Session' )->Set ( 'Error', true );

			return ( false );
		} else if ( !$Validate->Illegal ( $Directory, '/ * \ < > ( ) [ ] & ^ $ # @ ! ? ; \' " { } | ~ + = %20' ) ) {
			$this->GetSys ( 'Session' )->Set ( 'Message', __( 'Illegal Characters In Directory' ) );
			$this->GetSys ( 'Session' )->Set ( 'Error', true );

			return ( false );
		}

		if ( !isset ( $Set ) ) {
			// If the directory name already exists, throw an error.
			if ( $this->Sets->Get ( 'Total' ) > 0 ) {
				$this->GetSys ( 'Session' )->Set ( 'Message', __( 'Directory Already Exists' ) );
				$this->GetSys ( 'Session' )->Set ( 'Error', true );

				return ( false );
			}
			if ( !$Validate->Validate ( $fields, $data ) ) {
				$Reasons = $Validate->GetReasons();
				$Field = key($Reasons);
				$Reason = $Reasons[$Field][0];

				$this->GetSys ( 'Session' )->Set ( 'Message', __( $Reason, array ( 'field' => $Field ) ) );
				$this->GetSys ( 'Session' )->Set ( 'Error', true );

				return ( false );
			}
		} else { 
			// Check that the Set is proper
		}

		return ( true );
	}

	private function _PrepMessage ( ) {

		$markup = $this->View;

		$session = $this->GetSys ( 'Session' );
		$session->Context ( $this->Get ( 'Context' ) );

		if ( $message =  $session->Get ( 'Message' ) ) {
			$markup->Find ( '.add-message', 0 )->innertext = $message;
			if ( $error =  $session->Get ( 'Error' ) ) {
				$markup->Find ( '.add-message', 0 )->class .= ' error ';
			} else {
				$markup->Find ( '.add-message', 0 )->class .= ' message ';
			}
			$session->Destroy ( 'Message');
			$session->Destroy ( 'Error ');
		}

		return ( true );
	}

	private function _CreateDirectory ( $pUsername, $pDirectory ) {

		$location = ASD_PATH . '_storage/photos/' . $pUsername . '/' . $pDirectory;

		if ( file_exists ( $location ) ) return ( $location );

		if ( !rmkdir ( $location ) ) return ( false );

		return ( $location );
	}

	private function _RenameDirectory ( $pOld, $pNew ) {

		$Username = $this->_Focus->Username;

		$old = ASD_PATH . '_storage/photos/' . $Username . '/' . $pOld;
		$new = ASD_PATH . '_storage/photos/' . $Username . '/' . $pNew;

		if ( file_exists ( $new ) ) return ( false );

		if ( !rename ( $old, $new ) ) return ( false );

		return ( $new );
	}
}
