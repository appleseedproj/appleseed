<?php
/**
 * @version      $Id$
 * @package      Appleseed.Framework
 * @subpackage   System
 * @copyright    Copyright (C) 2004 - 2010 Michael Chisari. All rights reserved.
 * @link         http://opensource.appleseedproject.org
 * @license      GNU General Public License version 2.0 (See LICENSE.txt)
 */

// Temporary
$start_time = (float) array_sum(explode(' ',microtime())); 
$start_mem = memory_get_usage(); 

define ( 'APPLESEED', true );
define ( 'DS', DIRECTORY_SEPARATOR );
define ( 'ASD_PATH', $_SERVER['DOCUMENT_ROOT'] . DS);

define ( 'ASD_DOMAIN', $_SERVER['HTTP_HOST']);

require ( ASD_PATH . DS . 'system' . DS . 'base.php' );
require ( ASD_PATH . 'system' . DS . 'application.php' );

/** 
 * Entry Point
 * 
 */
 
global $zApp;
$zApp = new cApplication ( );

$zApp->Initialize ( );

$zApp->GetSys ( "Router" )->Route();

echo $zApp->GetSys ( "Buffer" )->Process ();

$end_mem = memory_get_usage(); 
$end_time = (float) array_sum(explode(' ',microtime())); 

$zApp->GetSys ( "Logs" )->Add ( "Benchmarks", ( $end_time - $start_time ), "_system" );
$zApp->GetSys ( "Logs" )->Add ( "Memory", ( $end_mem - $start_mem ), "_system" );

$zApp->GetSys ( "Event" )->Trigger ( "On", "System", "End" );
