<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Profile
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Profile Component Summary Controller
 * 
 * Profile Component Summary Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Profile
 */
class cProfileSummaryController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	public function Display ( $pView = null, $pData = array ( ) ) {
		
		$this->View = $this->GetView ( $pView ); 
		
		$this->Model = $this->GetModel ( "Summary" );
		
		$focus = $this->Talk ( 'User', 'Focus' );
		
		$qAndA = $this->Model->GetQuestionsAndAnswers ( $focus->uID );
		
		$list = $this->View->Find ( 'ul', 0 );
		
		$row = $this->View->Copy ( 'ul li' )->Find ( "li", 0 );
		$this->View->Find ( "ul", 0 )->innertext = "";
		
		$list->innertext = "";
		
		$row->Find ( "h2", 0 )->innertext = __("Full Name" );
		$row->Find ( "p", 0 )->innertext = $focus->Fullname;
		$list->innertext .= $row->outertext;
		
		foreach ( $qAndA as $questionAnswer ) {
			if ( !$questionAnswer->Answer ) continue;
			
			$row->Find ( "h2", 0 )->innertext = $questionAnswer->Question;
			$row->Find ( "p", 0 )->innertext = $questionAnswer->Answer;
			
			$list->innertext .= $row->outertext;
		}
		
		$names = explode ( " ", $focus->Fullname );
		$firstname = $names[0]; 
		$last = count ( $names ) - 1;
		$lastname = $names[$last]; 
		
		$this->View->Find ( '[id=summary-information-title]', 0 )->innertext = __ ( "Information For User", array ( "firstname" => $firstname, "lastname" => $lastname ) );
		
		$this->View->Display();
		
		return ( true );
	}
	
}

