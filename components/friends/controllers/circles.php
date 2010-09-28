<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Friends
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Friends Component Friends Controller
 * 
 * Friends Component Friends Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Friends
 */
class cFriendsCirclesController extends cController {
	
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
		
		if ( ( $this->_Focus->Username != $this->_Current->Username ) or ( $this->_Focus->Domain != $this->_Current->Domain ) ) {
			$this->GetSys ( "Foundation" )->Redirect ( "common/403.php" );
			return ( false );
		}
		
		$this->Add();
		
		return ( true );
	}
	
	public function ApplyToCircle ( $pView = null, $pData = array ( ) ) {
		
		if ( !$this->_CheckAccess ( ) ) {
			$this->GetSys ( "Foundation" )->Redirect ( "common/403.php" );
			return ( false );
		}
			
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		$this->Model = $this->GetModel ( "Circles" );
		
		$friend = $this->GetSys ( "Request" )->Get ( "Friend" );
		$circle = $this->GetSys ( "Request" )->Get ( "Circle" );
		$viewing = $this->GetSys ( "Request" )->Get ( "Viewing" );
		
		$this->Model->SaveFriendToCircle ( $this->_Focus->uID, $friend, $circle );
		
		if ( $viewing ) {
			$circleLink = $this->_ToUrl ( $viewing );
		} else {
			$circleLink = $this->_ToUrl ( $circle );
		}
		$redirect = 'http://' . ASD_DOMAIN . '/profile/' . $this->_Focus->Username . '/friends/' . $circleLink;
		header ( "Location:" . $redirect );
		
		return ( true );
	}
	
	public function Edit ( $pView = null, $pData = array ( ) ) {
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		// Check if circle exists
		if ( !$this->_CheckAccess ( ) ) {
			$this->GetSys ( "Foundation" )->Redirect ( "common/403.php" );
			return ( false );
		}
			
		if ( !$this->_Load() ) {
			$this->GetSys ( "Foundation" )->Redirect ( "common/404.php" );
			return ( false );
		}
		
		$this->View = $this->GetView ( "circles" ); 
		
		$this->_Prep();
		
		$this->View->Display();
		
		return ( true );
	}
	
	public function Add ( $pView = null, $pData = array ( ) ) {
		
		if ( !$this->_CheckAccess ( ) ) {
			$this->GetSys ( "Foundation" )->Redirect ( "common/403.php" );
			return ( false );
		}
		
		$this->View = $this->GetView ( "circles" ); 
		
		$this->_Prep();
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _CheckAccess ( ) {
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		if ( ( $this->_Focus->Username != $this->_Current->Username ) or ( $this->_Focus->Domain != $this->_Current->Domain ) ) {
			return ( false );
		}
		
		return ( true );
	}
	
	public function Save ( $pView = null, $pData = array ( ) ) {
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		$session = $this->GetSys ( "Session" );
		$session->Context ( $this->Get ( "Context" ) );
		
		if ( !$this->_CheckAccess ( ) ) {
			$this->GetSys ( "Foundation" )->Redirect ( "common/403.php" );
			return ( false );
		}
		
		$id = $this->GetSys ( "Request" )->Get ( "tID" );
		
		$validate = $this->GetSys ( "Validation" );
		
		// Validate the circle name
		$name = $this->GetSys ( "Request" )->Get ( "Name" );
		
		$this->_Load ( $name );
		
		if ( ( $this->Circles->Get ( "Total" ) > 0 ) and ( $this->Circles->Get ( "tID" ) != $id ) ) {
			$session->Set ( "Message", "Circle Already Exists" );
			$session->Set ( "Error", true );
			$error = true;
		}
		
		if ( !$validate->NotNull ( $name ) ) {
			// Name is null
			$session->Set ( "Message", "Circle Name Cannot Be Null" );
			$session->Set ( "Error", true );
			$error = true;
		} else if ( !$validate->Illegal ( $name, '-' ) ) {
			// Illegal characters
			$session->Set ( "Message", "Invalid Circle Name" );
			$session->Set ( "Error", true );
			$error = true;
		} else if ( in_array ( strtolower ( $name ), array ( "mutual", "requests", "circles" ) ) ) {
			// Reserved system names
			$session->Set ( "Message", "Invalid Circle Name" );
			$session->Set ( "Error", true );
			$error = true;
		}
		
		if ( $error ) {
			if ( $id ) {
				return ( $this->Edit() );
			} else {
				return ( $this->Add() );
			}
		}
		
		$this->Circles = $this->GetModel ( "Circles" );
		$circle = $this->GetSys ( "Request" )->Get ( "Name" );
		
		$this->Circles->SaveCircle ( $circle, $this->_Focus->Id, $id );
		
		$session->Context ( "friends.friends.(\d+).(mutual|friends|requests|circles|circle)" );
		$session->Set ( "Message", __( "Circle Has Been Saved", array ( "circle" => $circle ) ) );
		
		$circle = str_replace ( ' ', '-', strtolower ( $circle ) );
		$relocate = '/profile/' . $this->_Focus->Username . '/friends/' . $circle;
		$this->GetSys ( "Router" )->Redirect ( $relocate );
		return ( true );
	}
	
	public function Cancel ( $pView = null, $pData = array ( ) ) {
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		$session = $this->GetSys ( "Session" );
		$session->Context ( $this->Get ( "Context" ) );
		
		$id = $this->GetSys ( "Request" )->Get ( "tID" );
		
		$this->_Load();
		
		$original = $this->GetSys ( "Request" )->Get ( "Original" );
		$circle = $this->_ToUrl ( $original );
		
		$session->Context ( "friends.friends.(\d+).(mutual|friends|requests|circles|circle)" );
		if ( $id )
			$session->Set ( "Message", __( "Circle Edit Cancelled", array ( "circle" => $original ) ) );
		else
			$session->Set ( "Message", __( "Circle New Cancelled" ) );
		
		$relocate = '/profile/' . $this->_Focus->Username . '/friends/' . $circle;
		$this->GetSys ( "Router" )->Redirect ( $relocate );
		return ( true );
	}
	
	private function _ToUrl ( $pCircle ) {
		
		$return = strtolower ( utf8_encode ( urlencode ( str_replace ( ' ', '-', $pCircle ) ) ) );
		
		return ( $return );
	}
	
	private function _FromUrl ( $pCircle ) {
		
		$return = urldecode ( utf8_decode ( str_replace ( '-', ' ', $pCircle ) ) );
		
		return ( $return );
	}
	
	public function Remove ( $pView = null, $pData = array ( ) ) {
		
		$session = $this->GetSys ( "Session" );
		$session->Context ( $this->Get ( "Context" ) );
		
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		$this->_Current = $this->Talk ( 'User', 'Current' );
		
		if ( !$this->_CheckAccess ( ) ) {
			$this->GetSys ( "Foundation" )->Redirect ( "common/403.php" );
			return ( false );
		}
		
		$this->_Load();
		
		$this->Circles = $this->GetModel ( "Circles" );
		$circle = $this->_FromUrl ( $this->GetSys ( "Request" )->Get ( "Circle" ) );
		
		$this->Circles->DeleteCircle ( $circle, $this->_Focus->Id );
		
		$session->Context ( "friends.friends.(\d+).(mutual|friends|requests|circles|circle)" );
		$session->Set ( "Message", __( "Circle Has Been Removed", array ( "circle" => $circle ) ) );
		
		$relocate = '/profile/' . $this->_Focus->Username . '/friends/';
		$this->GetSys ( "Router" )->Redirect ( $relocate );
		
		return ( true );
	}
	
	/*
	 * Check to see if the circle exists.
	 * 
	 */
	private function _Load ( $pName = null ) {
		
		$this->Circles = $this->GetModel ( "Circles" );
		
		/*
		 * @todo This is too convoluted, clean up.
		 */
		if ( !$pName ) {
			$currentCircle = str_replace ( '-', ' ', urldecode ( strtolower ( $this->GetSys ( "Request" )->Get ( "Name" ) ) ) );
			if ( !$currentCircle ) $currentCircle = str_replace ( '-', ' ', urldecode ( strtolower ( $this->GetSys ( "Request" )->Get ( "Circle" ) ) ) );
			if ( !$currentCircle ) return ( false );
		} else {
			$currentCircle = $pName;
		}
		
		$this->Circles->Load ( $this->_Focus->Id, $currentCircle );
		
		if ( $this->Circles->Get ( "Total" ) == 0 ) return ( false );
		
		$this->Circles->Fetch();
		
		return ( true );
	}
	
	private function _Prep ( ) {
		
		$this->Circles = $this->GetModel ( "Circles" );
		
		$data = (array) $this->Circles->Get ( "Data" );
		
		$this->View->Synchronize ( $data );
		
		$this->View->Find ( "[class=friends-circles-form]", 0 )->action = '/profile/' . $this->_Focus->Username . '/friends/circles/';
		
		$context = $this->Get ( "Context" );
		
		$this->View->Find ( "[name=Context]", 0 )->value = $context;
		$this->View->Find ( "[name=Original]", 0 )->value = $this->Circles->Get ( "Name" );
		
		if ( $this->Circles->Get ( "tID" ) ) {
			$this->View->Find ( "[class=friends-circles-title]", 0 )->innertext = __( "Edit Circles Header" , array ( "circle" => $this->Circles->Get ( "Name" ) ) );
		} else {
			$this->View->Find ( "[class=friends-circles-title]", 0 )->innertext = __( "New Circles Header" );
		}
		
		$this->_PrepMessage();
		
		return ( true );
	}
	
	private function _PrepMessage ( ) {
		
		$markup = $this->View;
		
		$session = $this->GetSys ( "Session" );
		$session->Context ( $this->Get ( "Context" ) );
		
		if ( $message =  $session->Get ( "Message" ) ) {
			$markup->Find ( "[id=friends-circles-message]", 0 )->innertext = $message;
			if ( $error =  $session->Get ( "Error" ) ) {
				$markup->Find ( "[id=friends-circles-message]", 0 )->class = "error";
			} else {
				$markup->Find ( "[id=friends-circles-message]", 0 )->class = "message";
			}
			$session->Delete ( "Message ");
			$session->Delete ( "Error ");
		}
		
		return ( true );
	}
	
}
