<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 12/10/2016
 * Time: 5:38 PM
 */
 
include_once('../Includes/Common.php');

if (session_status() == PHP_SESSION_NONE) 
{
    session_start();
	
	if ( !(isset( $_SESSION[\Common\Security::$SessionCartArrayKey] ) && is_array($_SESSION[\Common\Security::$SessionCartArrayKey]) ) )
	{
		$_SESSION[\Common\Security::$SessionCartArrayKey] = array();
		
		$_SESSION[\Common\Security::$SessionCartArrayKey][1] = 3;
		$_SESSION[\Common\Security::$SessionCartArrayKey][2] = 10;
	}
}