<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 12/10/2016
 * Time: 5:38 PM
 */
 
 ini_set("display_errors", "1");
 
 include_once("Common.php");

//if (session_status() == PHP_SESSION_NONE) 
//{
    session_start();
	
	if ( !(isset( $_SESSION[\Common\Security::$SessionCartArrayKey] ) && is_array($_SESSION[\Common\Security::$SessionCartArrayKey]) ) )
	{
		$_SESSION[\Common\Security::$SessionCartArrayKey] = array();
		
		$_SESSION[\Common\Security::$SessionCartArrayKey][1] = 3;
		$_SESSION[\Common\Security::$SessionCartArrayKey][2] = 10;
		$_SESSION[\Common\Security::$SessionCartArrayKey][3] = 17;
	}
//}