<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   ProfileSummary
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Profile Component Summary Model
 * 
 * Profile Component Summary Model Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Profile
 */
class cProfileSummaryModel extends cModel {
	
	protected $_Tablename = "userQuestions";
	
	protected $_Answers;
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( $pTables = null ) {       
		
		$this->_Answers = new cModel ( "userAnswers" );
		
		parent::__construct( $pTables );
	}
	
	public function GetQuestionsAndAnswers ( $pUserId ) {
		
		$this->Retrieve ();
		
		while ( $this->Fetch() ) {
			$return[] = new stdClass();
			$current = count ( $return ) - 1;
			
			$return[$current]->Question = $this->Get ( "FullQuestion" );
			
			$tID = $this->Get ( "tID" );
			$this->_Answers->Retrieve ( array ( "userAuth_uID" => $pUserId, "userQuestions_tID" => $tID ) );
			
			$this->_Answers->Fetch();
			
			$return[$current]->Answer = $this->_Answers->Get ( "Answer" );
		}
		
		return ( $return );
	}
	
}
