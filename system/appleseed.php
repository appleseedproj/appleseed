<?php
/**
 * @version      $Id$
 * @package      Appleseed.Framework
 * @subpackage   System
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

define ( 'APPLESEED', true );
define ( 'DS', DIRECTORY_SEPARATOR );
define ( 'ASD_PATH', $_SERVER['DOCUMENT_ROOT'] . DS);

require_once ( ASD_PATH . DS . 'system' . DS . 'base.php' );
require_once ( ASD_PATH . 'system' . DS . 'application.php' );

/** 
 * Entry Point
 * 
 */

global $zApp;
$zApp = new cApplication ( );

$zApp->Initialize ( );

$zApp->Router->Route ( );

echo $zApp->Buffer->Process ();