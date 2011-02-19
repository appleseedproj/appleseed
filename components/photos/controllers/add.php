<?php
/**
 * @version	  $Id$
 * @package	  Appleseed.Components
 * @subpackage   Photos
 * @copyright	Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link		 http://opensource.appleseedproject.org
 * @license	  GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Photos Component Add Controller
 * 
 * Photos Component Add Controller Class
 * 
 * @package	 Appleseed.Components
 * @subpackage  Photos
 */
class cPhotosAddController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {	   
		$this->Image = $this->GetSys ( 'Image' );

		parent::__construct( );
	}

	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		
		$this->View = $this->GetView ( $pView ); 
		
		$this->Sets = $this->GetModel ( 'Sets' );
		$this->Photos = $this->GetModel ( 'Photos' );

		$this->Sets->Load ( $this->_Focus->Id );

		$this->Photos->Load ( $Identifier );
		$this->Photos->Fetch();

		$this->_Prep();
		
		$this->View->Synchronize();
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _Prep ( ) {

		$this->View->Find ( '[name="Set"]', 0 )->innertext .= '<optgroup>';
		while ( $this->Sets->Fetch() ) {
			$option = '<option value="' . $this->Sets->Get ( 'Set_PK' ) . '">' . $this->Sets->Get ( 'Name' ) . '</option>';
			$this->View->Find ( '[name="Set"]', 0 )->innertext .= $option;
		}
		$this->View->Find ( '[name="Set"]', 0 )->innertext .= '</optgroup>';

		$this->View->Find ( '[name="Context"]', 0 )->value = $this->Get ( 'Context' );
			
		$privacyData = array ( 'Type' => 'journal', 'Identifier'  => $Identifier );
		$privacyControls =  $this->View->Find ('.privacy');
		foreach ( $privacyControls as $c => $control ) {
			$control->innertext = $this->GetSys ( 'Components' )->Buffer ( 'privacy', $privacyData );
		}

		$this->_PrepMessage();

		return ( true );
	}

	public function Save ( ) {

		$Request = $this->GetSys ( 'Request' );
		$Files = $Request->Files();

		$this->Sets = $this->GetModel ( 'Sets' );
		$this->Photos = $this->GetModel ( 'Photos' );

		// 0. Determine access.
		if ( !$this->_CheckAccess ( ) ) {
			$this->GetSys ( 'Foundation' )->Redirect ( 'common/403.php' );
			return ( false );
		}

		// 1. Sanity check all the inputs.
		$Set = (int)$Request->Get ( 'Set' );
		$Directory = $Request->Get ( 'Directory' );

		if ( $Set == -1 ) 
			$this->Sets->Load ( $this->_Focus->Id, $Directory );
		else
			$this->Sets->Load ( $this->_Focus->Id, $Set );

		if ( !$this->_Sanity() ) {
			return ( $this->Display( 'add' ) );
		}

		// 3. Create an album if necessary.
		if ( $Set == -1 ) {
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
		} else {
			$Directory = $this->Sets->Get ( 'Directory' );

			$Location = ASD_PATH . '_storage/photos/' . $this->_Focus->Username . '/' . $Directory;
		}

		// 4. Resize and move photo(s).
		$this->_Process ( $this->Sets->Get ( 'Set_PK' ), $Location, $Files );

		// 5. Store the photo information.

		// 6. Redirect to the new photo.

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
		$Files = $Request->Files();

		if ( !$Files ) {
			$this->GetSys ( 'Session' )->Set ( 'Message', __( 'No File Uploaded' ) );
			$this->GetSys ( 'Session' )->Set ( 'Error', true );
			return ( false );
		}

		$Set = $Request->Get ( 'Set' );
		$Name = $Request->Get ( 'Name' );
		$Directory = $Request->Get ( 'Directory' );
		$Description = $Request->Get ( 'Description' );

		$fields = $this->Sets->Get ( 'Fields' );
		$data = $Request->Get();

		if ( $Set == -1 ) {
			if ( !$Directory ) {
				$this->GetSys ( 'Session' )->Set ( 'Message', __( 'Cannot Be Null', array ( 'field' => 'Directory' ) ) );
				$this->GetSys ( 'Session' )->Set ( 'Error', true );

				return ( false );
			} else if ( !$Validate->Illegal ( $Directory, '/ * \ < > ( ) [ ] & ^ $ # @ ! ? ; \' " { } | ~ + = %20' ) ) {
				$this->GetSys ( 'Session' )->Set ( 'Message', __( 'Illegal Characters In Directory' ) );
				$this->GetSys ( 'Session' )->Set ( 'Error', true );

				return ( false );
			}
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

	private function _Process ( $pSetId, $pLocation, $pFiles ) {

		if ( !is_writable ( $pLocation ) ) return ( false );

		$Descriptions = $this->GetSys ( 'Request' )->Get ( 'Descriptions' );
		foreach ( $pFiles as $f => $file ) {
			$Description = $Descriptions[$f];

			$TempFile = $file['tmp_name'][0];
			$Filename = $file['name'][0];

			$Identifier = $this->CreateUniqueIdentifier();
			$Extension = '.jpg';

			$ThumbFilename = $pLocation . '/' . $Identifier . '.t' . $Extension;
			$this->_ResizeAndMove ( $TempFile, $ThumbFilename, 32, 32, true );

			$SmallFilename = $pLocation . '/' . $Identifier . '.s' . $Extension;
			$this->_ResizeAndMove ( $TempFile, $SmallFilename, 64, 64, true );

			$MediumFilename = $pLocation . '/' . $Identifier . '.m' . $Extension;
			$this->_ResizeAndMove ( $TempFile, $MediumFilename, 128, 128, true );

			$NormalFilename = $pLocation . '/' . $Identifier . '.n' . $Extension;
			$this->_ResizeAndMove ( $TempFile, $NormalFilename, 800 );

			$OriginalFilename = $pLocation . '/' . $Identifier . $Extension;
			$this->_ResizeAndMove ( $TempFile, $OriginalFilename );

			// @todo: Move to model
			$this->Photos->Destroy ( 'Photo_PK' );
			$this->Photos->Set ( 'Owner_FK', $this->_Focus->Id );
			$this->Photos->Set ( 'Set_FK', $pSetId );
			$this->Photos->Set ( 'Description', $Description );
			$this->Photos->Set ( 'Filename', $Filename );
			$this->Photos->Set ( 'Identifier', $Identifier );
			$this->Photos->Set ( 'Created', NOW() );
			$this->Photos->Set ( 'Updated', NOW() );
			$this->Photos->Set ( 'Profile', false );
			$this->Photos->Save();
		}

		return ( true );
	}

	private function _ResizeAndMove ( $pSource, $pDestination, $pWidth = null, $pHeight = null, $pProportional = false ) {

		$this->Image->Attributes ( $pSource );
		$this->Image->Convert ( $pSource );

		if ( ( $pWidth ) && ( $pHeight ) ) {
			$this->Image->ResizeAndCrop ( $pWidth, $pHeight );
			$this->Image->Save ( $pDestination );
		} else if ( ( !$pWidth ) && ( $pHeight ) ) {
			if ( !$pWidth ) $pWidth = $this->Image->Get ( 'Width' );
			if ( !$pHeight ) $pHeight = $this->Image->Get ( 'Height' );

			if ( $this->Image->Get ( 'Height' ) > $pHeight ) 
    			$this->Image->Resize ($pWidth, $pHeight, true, false, true );
			$this->Image->Save ( $pDestination );
		} else if ( ( $pWidth ) && ( !$pHeight ) ) {
			if ( !$pWidth ) $pWidth = $this->Image->Get ( 'Width' );
			if ( !$pHeight ) $pHeight = $this->Image->Get ( 'Height' );

			if ( $this->Image->Get ( 'Width' ) > $pWidth ) {
    			$this->Image->Resize ($pWidth, $pHeight, true, true );
			}

			$this->Image->Save ( $pDestination ) or die ( 'Couldn\'t ');
		} else {
			$this->Image->Save ( $pDestination );
		}

		return ( true );
	}

}