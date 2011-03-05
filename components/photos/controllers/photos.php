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

/** Photos Component Photos Controller
 * 
 * Photos Component Photos Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Photos
 */
class cPhotosPhotosController extends cController {
	
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
		
		$this->Set = $this->GetModel ( 'Sets' );

		$this->Photos = $this->GetModel ( 'Photos' );

		$Set = $this->GetSys ( 'Request' )->Get ( 'Set' );

		$this->Set->Load ( $this->_Focus->Id, $Set );

		if ( $this->Set->Get ( 'Total' ) == 0 ) {
            $this->GetSys ( 'Foundation' )->Redirect ( 'common/404.php' );
            return ( false );
		}

		$this->Set->Fetch();

		$Access = $this->Talk ( 'Privacy', 'Check', $data = array ( 'Requesting' => $this->_Current->Account, 'Type' => 'Photosets', 'Identifier' => $this->Set->Get ( 'Identifier' ) ) );

		if ( ( !$Access ) && ( $this->_Current->Account != $this->_Focus->Account ) ) {
			if ( !$this->_Current->Account ) {
				$this->GetSys ( 'Session' )->Context ( 'login.login.(\d)+.login' );
				$this->GetSys ( 'Session' )->Set ( 'Message', __( 'Login To See This Page' ) );
				$this->GetSys ( 'Session' )->Set ( 'Error', true );
				$this->GetSys ( 'Foundation' )->Redirect ( 'login/login.php' );
			} else {
				$this->GetSys ( 'Foundation' )->Redirect ( 'common/denied.php' );
			}
			return ( false );
		}
		
		$this->Photos->LoadFromSet ( $this->Set->Get ( 'Set_PK' ) );

		$this->_Prep();
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _Prep ( ) {

		$Contexts = $this->View->Find ( '[name="Context"]' );
		foreach ( $Contexts as $c => $context ) {
			$context->value = $this->Get ( 'Context' );
		}

		if ( $this->_Current->Account == $this->_Focus->Account ) {
			$this->View->Find ( 'form[class="edit"]', 0 )->action = '/profile/' . $this->_Focus->Username . '/photos/';
			$this->View->Find ( 'form[class="edit"] [name="Context"]', 0 )->value = "sets.photos.(\d+).sets";
			$this->View->Find ( '[name="Set"]', 0 )->value = $this->Set->Get ( 'Set_PK' );
			$this->View->Find ( 'form[class="add"]', 0 )->action = '/profile/' . $this->_Focus->Username . '/photos/' . $this->Set->Get ( 'Directory' ) . '/';
		} else {
			$this->View->Find ( 'form[class="edit"]', 0 )->outertext = '';
			$this->View->Find ( 'form[class="add"]', 0 )->outertext = '';
		}

		$list = $this->View->Find ( '.list', 0);

		$item = $this->View->Find ( '.item', 0 );

		$list->innertext = "";
		
		while ( $this->Photos->Fetch() ) {
			$id = $this->Photos->Get ( 'Photo_PK' );

			$Identifier = $this->Photos->Get ( 'Identifier' );

			$filename = $this->Photos->Get ( 'Filename' );
			$extension = pathinfo ( $filename, PATHINFO_EXTENSION );

			$link = 'http://' . ASD_DOMAIN . '/profile/' . $this->_Focus->Username . '/photos/' . $this->Set->Get ( 'Directory' ) . '/' . $this->Photos->Get ( 'Identifier' );
			$item->Find ( '.photo-link', 0 )->href = $link;

			list ( $file ) = explode ( '.' . $extension, $filename );

			$photoLocation = 'http://' . ASD_DOMAIN . '/_storage/photos/' . $this->_Focus->Username . '/' . $this->Set->Get ( 'Directory' ) . '/' . $Identifier . '.m' . '.jpg'; 
			$item->Find ( '.photo', 0 )->src = $photoLocation;

			$item->Find ( '.description', 0 )->innertext = $this->Photos->Get ( 'Description' );
			$list->innertext .= $item->outertext;
		}
	}

	public function Add ( $pView = null, $pData = array ( ) ) {
		
		// Determine access.
		if ( !$this->_CheckAccess ( ) ) {
			$this->GetSys ( 'Foundation' )->Redirect ( 'common/403.php' );
			return ( false );
		}

		$this->View = $this->GetView ( 'photos.add' ); 
		
		$this->Sets = $this->GetModel ( 'Sets' );
		$this->Photos = $this->GetModel ( 'Photos' );

		$this->Sets->Load ( $this->_Focus->Id );

		$this->Photos->Load ( $Identifier );
		$this->Photos->Fetch();

		$this->_PrepAdd();
		
		$this->View->SynchronizeInputs();
		
		$this->View->Display();
		
		return ( true );
	}

	private function _PrepAdd ( ) {

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

        $this->Image = $this->GetSys ( 'Image' );

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
		$Set = $Request->Get ( 'Set' );

		$this->Sets->Load ( $this->_Focus->Id, $Set );

		if ( !$this->_Sanity() ) {
			return ( $this->Display( 'add' ) );
		}

		$Directory = $this->Sets->Get ( 'Directory' );

		$Location = ASD_PATH . '_storage/photos/' . $this->_Focus->Username . '/' . $Directory;

		// 2. Resize and move photo(s) and store information.
		$this->_Process ( $this->Sets->Get ( 'Set_PK' ), $Location, $Files );

		// 3. Redirect to the new photo.
		$Redirect = 'http://' . ASD_DOMAIN . '/profile/' . $this->_Focus->Username . '/photos/' . $Directory . '/';
        header ( "Location:" . $Redirect );


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

		$fileCount = count ( $pFiles[0]['tmp_name'] );

		for ( $c = 0; $c < $fileCount; $c++ ) {
			$Files[$c] = array ();
			$Files[$c]['name'] = $pFiles[0]['name'][$c];
			$Files[$c]['type'] = $pFiles[0]['type'][$c];
			$Files[$c]['tmp_name'] = $pFiles[0]['tmp_name'][$c];
			$Files[$c]['error'] = $pFiles[0]['error'][$c];
			$Files[$c]['size'] = $pFiles[0]['size'][$c];
		}

		foreach ( $Files as $f => $file ) {
			$Description = $Descriptions[$f];

			$TempFile = $file['tmp_name'];
			$Filename = $file['name'];

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

			$this->_AddToNewsfeed ( $Description );
		}

		return ( true );
	}

	private function _AddToNewsfeed ( $pDescription ) {
		// Send out notifications
		$friends = $this->Talk ( 'Friends', 'Friends' );
           
		foreach ( $friends as $f => $friend ) {
			if ( $friend == $this->_Current->Account ) continue;
			$Access = $this->Talk ( 'Privacy', 'Check', array ( 'Requesting' => $friend, 'Type' => 'Photosets', 'Identifier' => $this->Sets->Get ( 'Identifier' ) ) );
			if ( !$Access ) unset ( $friends[$f] );
		}
		 
		$Link = 'http://' . ASD_DOMAIN . '/profile/' . $this->_Focus->Username . '/photos/' . $this->Sets->Get ( 'Directory' ) . '/' . $this->Photos->Get ( 'Identifier' );
		$ContextLink = 'http://' . ASD_DOMAIN . '/profile/' . $this->_Focus->Username . '/photos/' . $this->Sets->Get ( 'Directory' ) . '/';
		$ThumbLink = 'http://' . ASD_DOMAIN . '/_storage/photos/' . $this->_Focus->Username . '/' . $this->Sets->Get ( 'Directory' ) . '/' . $this->Photos->Get ( 'Identifier' ) . '.s.jpg';

		$notifyData = array ( 
			'OwnerId' => $this->_Focus->Id, 
			'Friends' => $friends, 
			'Icon' => $ThumbLink, 
			'ActionOwner' => $this->_Focus->Account, 
			'Action' => 'added a photo', 
			'ActionLink' => $Link, 
			'ContextLink' => $ContextLink, 
			'ContextOwner' => $this->_Focus->Account, 
			'Context' => 'photo', 
			'Description' => $pDescription, 
			'Identifier' => $this->Photos->Get ( 'Identifier' ) 
		);

		$this->Talk ( 'Newsfeed', 'Notify', $notifyData );

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
