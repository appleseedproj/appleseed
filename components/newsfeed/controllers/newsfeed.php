<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Newsfeed
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Newsfeed Component Newsfeed Controller
 * 
 * Newsfeed Component Newsfeed Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Newsfeed
 */
class cNewsfeedNewsfeedController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$this->_Current = $this->Talk ( 'User', 'Current' );
		$this->_Focus = $this->Talk ( 'User', 'Focus' );
		
		$this->Model = $this->GetModel ( 'Incoming' );
		
		$this->View = $this->GetView ( $pView );
		
		$this->_Prep();
		
		$this->View->Display();
		
		return ( true );
	}
	
	private function _Prep ( ) {
		
		$this->Model->Incoming ( $this->_Focus->Id );
		
		$li = $this->View->Find ( '.list .item', 0);
		
		$row = $this->View->Copy ( '.list' )->Find ( '.item', 0 );
		
		$rowOriginal = $row->outertext;
		
		$li->innertext = '';
		
		if ( $this->Model->Get ( 'Total' ) == 0 ) $li->outertext = '';
		
		while ( $this->Model->Fetch() ) {
			$sentenceData['ActionOwner'] = $ActionOwner = $this->Model->Get ( 'ActionOwner' );
			$sentenceData['Action'] = $Action = $this->Model->Get ( 'Action' );
			$sentenceData['ActionLink'] = $ActionLink = $this->Model->Get ( 'ActionLink' );
			$sentenceData['SubjectOwner'] = $SubjectOwner = $this->Model->Get ( 'SubjectOwner' );
			$sentenceData['Context'] = $Context = $this->Model->Get ( 'Context' );
			$sentenceData['ContextOwner'] = $ContextOwner = $this->Model->Get ( 'ContextOwner' );
			$sentenceData['ContextLink'] = $ContextLink = $this->Model->Get ( 'ContextLink' );
			$sentenceData['Icon'] = $Icon = $this->Model->Get ( 'Icon' );
			$sentenceData['Comment'] = $Comment = $this->Model->Get ( 'Comment' );
			$sentenceData['Description'] = $Description = $this->Model->Get ( 'Description' );
			$sentenceData['Identifier'] = $Identifier = $this->Model->Get ( 'Identifier' );
			$sentenceData['Updated'] = $Updated = $this->Model->Get ( 'Updated' );
			$sentenceData['Created'] = $Created = $this->Model->Get ( 'Created' );
			
			$row = new cHTML ();
			$row->Load ( $rowOriginal );
			
			$row->Find ( '.stamp', 0 )->innertext = $this->GetSys ( 'Date' )->Format ( $Updated );
			$row->Find ( '.comment', 0 )->innertext = str_replace ( "\n", "<br />", $this->Model->Get ( 'Comment' ) );
			$row->Find ( '.actionowner-link', 0 )->rel = $ActionOwner;
			$row->Find ( '.actionowner-link', 0 )->innertext = $ActionOwner;
			$row->Find ( '.remove', 0 )->innertext = '';
			
			list ( $username, $domain ) = explode ( '@', $ActionOwner );
			$data = array ( 'username' => $username, 'domain' => $domain, 'width' => 64, 'height' => 64 );
			$row->Find ( '.actionowner-icon', 0 )->src = $this->GetSys ( 'Event' )->Trigger ( 'On', 'User', 'Icon', $data );
			
			$row->Find ( '.sentence', 0 )->innertext = $this->_Sentence ( $sentenceData );
			
			$data = array ( 'account' => $ActionOwner, 'source' => ASD_DOMAIN );
			$OwnerLink = $this->GetSys ( 'Event' )->Trigger ( 'Create', 'User', 'Link', $data );
			$row->Find ( '.actionowner-link', 0 )->href = $OwnerLink;
			$row->Find ( '.actionowner-icon-link', 0 )->href = $OwnerLink;
			
			$row->Find ( '[name=Context]', 0 )->value = $this->Get ( 'Context' );
			$row->Find ( '[name=Identifier]', 0 )->value = $Identifier;
			
		    $li->innertext .= $row->outertext;
		    unset ( $row );
		}
		
		$this->View->Reload();
	}
	
	private function _Sentence ( $pData ) {
			$ActionOwner = $sentenceData['ActionOwner'] = $this->Model->Get ( 'ActionOwner' );
			$Action = $sentenceData['Action'] = $Action = $this->Model->Get ( 'Action' );
			$ActionLink = $sentenceData['ActionLink'] = $ActionLink = $this->Model->Get ( 'ActionLink' );
			$SubjectOwner = $sentenceData['SubjectOwner'] = $SubjectOwner = $this->Model->Get ( 'SubjectOwner' );
			$Context = $sentenceData['Context'] = $Context = $this->Model->Get ( 'Context' );
			$ContextOwner = $sentenceData['ContextOwner'] = $ContextOwner = $this->Model->Get ( 'ContextOwner' );
			$ContextLink = $sentenceData['ContextLink'] = $ContextLink = $this->Model->Get ( 'ContextLink' );
			$Icon = $sentenceData['Icon'] = $Icon = $this->Model->Get ( 'Icon' );
			$Comment = $sentenceData['Comment'] = $Comment = $this->Model->Get ( 'Comment' );
			$Description = $sentenceData['Description'] = $Description = $this->Model->Get ( 'Description' );
			$Identifier = $sentenceData['Identifier'] = $Identifier = $this->Model->Get ( 'Identifier' );
			$Updated = $sentenceData['Updated'] = $Update = $this->Model->Get ( 'Updated' );
			$Created = $sentenceData['Created'] = $Created = $this->Model->Get ( 'Created' );
			
			switch ( $Context ) {
				case 'page':
					$actionowner = "<a href=\"be.com\">" . $ActionOwner . "</a>";
					$contextowner = "<a href=\"be.com\">" . $ContextOwner . "</a>";
					//$actionowner = "<a href=\"be.com\">" . "Cerie Xerox" . "</a>";
					//$contextowner = "<a href=\"be.com\">" . "Liz Lemon" . "</a>";
					if ( ( $ActionOwner == $this->_Focus->Account ) && ( $ActionOwner == $ContextOwner ) ) {
						$return = __( "You Updated Your Status", array ( 'actionowner' => $actionowner ) );
					} else if ( ( $ContextOwner == $this->_Focus->Account ) && ( $ActionOwner != $ContextOwner ) ) {
						$return = __( "Someone Posted On Your Page", array ( 'actionowner' => $actionowner ) );
					} else if ( ( $ActionOwner == $this->_Focus->Account ) && ( $ActionOwner != $ContextOwner ) ) {
						$return = __( "You Posted On A Page", array ( 'contextowner' => $contextowner ) );
					} else if ( $ContextOwner == $ActionOwner ) {
						$return = __( "Someone Updated Their Status", array ( 'actionowner' => $actionowner ) );
					} else {
						$return = __( "Someone Posted On A Page", array ( 'actionowner' => $actionowner, 'contextowner' => $contextowner ) );
					}
				break;
			}
			
			return ( $return );
	}
	
	public function Notify ( $pView = null, $pData = array ( ) ) {
		
		$OwnerId = $pData['OwnerId'];
		$Action = $pData['Action'];
		$ActionOwner = $pData['ActionOwner'];
		$ActionLink = $pData['ActionLink'];
		$SubjectOwner = $pData['SubjectOwner'];
		$Context = $pData['Context'];
		$ContextOwner = $pData['ContextOwner'];
		$ContextLink = $pData['ContextLink'];
		$Icon = $pData['Icon'];
		$Comment = $pData['Comment'];
		$Description = $pData['Description'];
		$Identifier = $pData['Identifier'];
		
		$Friends = $pData['Friends'];
		
		$Incoming = $this->GetModel ( 'Incoming' );
		$Outgoing = $this->GetModel ( 'Outgoing' );
		
		$Incoming->Queue ( $OwnerId, $Action, $ActionOwner, $ActionLink, $SubjectOwner, $Context, $ContextOwner, $ContextLink, $Icon, $Comment, $Description, $Identifier );
		
		foreach ( $Friends as $f => $friend ) {
			list ( $username, $domain ) = explode ( '@', $friend );
			if ( $domain == ASD_DOMAIN ) {
				// Process a local request
				$Recipient = $this->Talk ( 'User', 'Account', array ( 'Username' => $username ) );
				$Incoming->Queue ( $Recipient->Id, $Action, $ActionOwner, $ActionLink, $SubjectOwner, $Context, $ContextOwner, $ContextLink, $Icon, $Comment, $Description, $Identifier );
			} else {
				// Add remote requests to the outgoing queue.
				$Outgoing->Queue ( $OwnerId, $friend, $Action, $ActionOwner, $ActionLink, $SubjectOwner, $Context, $ContextOwner, $ContextLink, $Icon, $Comment, $Description, $Identifier );
			}
		}
		
		return ( true );
	}
	
	public function AddToIncoming ( $pView = null, $pData = array ( ) ) {
		
		$OwnerId = $pData['OwnerId'];
		$Action = $pData['Action'];
		$ActionOwner = $pData['ActionOwner'];
		$ActionLink = $pData['ActionLink'];
		$SubjectOwner = $pData['SubjectOwner'];
		$Context = $pData['Context'];
		$ContextOwner = $pData['ContextOwner'];
		$ContextLink = $pData['ContextLink'];
		$Icon = $pData['Icon'];
		$Comment = $pData['Comment'];
		$Description = $pData['Description'];
		$Identifier = $pData['Identifier'];
		$Created = $pData['Created'];
		$Updated = $pData['Updated'];
		
		$Incoming = $this->GetModel ( 'Incoming' );
		
		$Incoming->Queue ( $OwnerId, $Action, $ActionOwner, $ActionLink, $SubjectOwner, $Context, $ContextOwner, $ContextLink, $Icon, $Comment, $Description, $Identifier, $Created, $Updated );
		
		return ( true );
	}
	
	public function ProcessQueue ( $pView = null, $pData = array ( ) ) {
		
		$this->Outgoing = $this->GetModel ( 'Outgoing' );
		
		$this->Outgoing->Retrieve();
		
		while ( $this->Outgoing->Fetch() ) {
			$Id = $this->Outgoing->Get ( 'Outgoing_PK' );
			$OwnerId = $this->Outgoing->Get ( 'Owner_FK' );
			$data['Account'] = $this->Talk ( 'User', 'Account', array ( 'Id' => $OwnerId ) )->Account;
			$Recipient = $data['Recipient'] = $this->Outgoing->Get ( 'Recipient' );
			$Action = $data['Action'] = $this->Outgoing->Get ( 'Action' );
			$ActionOwner = $data['ActionOwner'] = $this->Outgoing->Get ( 'ActionOwner' );
			$ActionLink = $data['ActionLink'] = $this->Outgoing->Get ( 'ActionLink' );
			$SubjectOwner = $data['SubjectOwner'] = $this->Outgoing->Get ( 'SubjectOwner' );
			$Context = $data['Context'] = $this->Outgoing->Get ( 'Context' );
			$ContextOwner = $data['ContextOwner'] = $this->Outgoing->Get ( 'ContextOwner' );
			$ContextLink = $data['ContextLink'] = $this->Outgoing->Get ( 'ContextLink' );
			$Icon = $data['Icon'] = $this->Outgoing->Get ( 'Icon' );
			$Comment = $data['Comment'] = $this->Outgoing->Get ( 'Comment' );
			$Description = $data['Description'] = $this->Outgoing->Get ( 'Description' );
			$Identifier = $data['Identifier'] = $this->Outgoing->Get ( 'Identifier' );
			$Created = $data['Created'] = $this->Outgoing->Get ( 'Created' );
			$Updated = $data['Updated'] = $this->Outgoing->Get ( 'Updated' );
			
			if ( $this->GetSys ( 'Event' )->Trigger ( 'On', 'Feed', 'Synchronize', $data ) ) {
				$successful[] = $Id;
			};
		}
		
		$successful = join (',', $successful );
		$this->Outgoing->Delete ( array ( 'Outgoing_PK' => '()' . $successful ) );
		
		return ( true );
	}
}
