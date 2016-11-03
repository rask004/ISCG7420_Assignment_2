<?php

ini_set("display_errors","1");

include_once('../Session.php');
include_once("../CapManager.php");
include_once('../Common.php');

if (!isset($_REQUEST["d"]))
{
	// redirect to AJAX error page.
	$_SESSION["last_Error"] = "AJAX_Error";
	$_SESSION["Error_MSG"] = "Orders ajax page: ";
	if (count($_REQUEST) == 0)
	{
		$_SESSION["Error_MSG"] .= "Empty Query String.";
	}
	else
	{
		foreach($_REQUEST as $key=>$value)
		{
			$_SESSION["Error_MSG"] .= $key . "=" . $value . "; ";		
		}
	}
	
	header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/AJAX_Error.php");
	exit;
}
else
{
	$capId = (integer) ($_REQUEST["d"] + 0);
	
	if (isset($_SESSION[\Common\Security::$SessionCartArrayKey][$capId]))
	{
		unset($_SESSION[\Common\Security::$SessionCartArrayKey][$capId]);
	}
}