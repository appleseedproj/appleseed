<?php
/**
 * @version      $Id$
 * @package      Appleseed.Components
 * @subpackage   Options
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Restrict direct access
defined( 'APPLESEED' ) or die( 'Direct Access Denied' );

/** Options Component Controller
 * 
 * Options Info Component Controller Class
 * 
 * @package     Appleseed.Components
 * @subpackage  Options
 */
class cOptionsInfoController extends cController {
	
	/**
	 * Constructor
	 *
	 * @access  public
	 */
	public function __construct ( ) {       
		parent::__construct( );
	}
	
	function Display ( $pView = null, $pData = array ( ) ) {

		$Questions = $this->_Questions();
		foreach ( $Questions as $g => $Group ) {
			echo $Group->Label, "<br />";
			foreach ( $Group->Questions as $q => $Question) {
				echo '&nbsp; ' . $Question->Label, "<br />";
			}
		}

		return ( true );
		return ( parent::Display( $pView, $pData ) );
	}

	/*
	 * Combine question configuration into array of class objects.
	 *
	 */
	private function _Questions ( ) {
		$Config = $this->Get ( 'Config' );
		$quests = $Config['questions'];
		$groups = $Config['question_groups'];
		foreach ( $groups as $g => $group ) {
			$Groups[$group] = new stdClass();
			$Groups[$group]->Label = $Config['question_group_labels'][$g];
			foreach ( $quests as $q => $quest ) {
				if ( $Config['question_group'][$q] != $g ) continue;
				$Groups[$group]->Questions[$quest] = new stdClass();
				$Groups[$group]->Questions[$quest]->Label = $Config['question_labels'][$q];
				$Groups[$group]->Questions[$quest]->Type = $Config['question_types'][$q];
				$Groups[$group]->Questions[$quest]->Field = $Config['question_fields'][$q];
				$Groups[$group]->Questions[$quest]->Social = $Config['question_social'][$q];
				$Groups[$group]->Questions[$quest]->Display = $Config['question_display'][$q];
				if ( $Config['question_options'][$q] )
					$Groups[$group]->Questions[$quest]->Options = explode ( '|', $Config['question_options'][$q] );
			}
			ksort ( $Groups[$group]->Questions );
		}
		ksort ( $Groups );
		return ( $Groups );
	}
}
