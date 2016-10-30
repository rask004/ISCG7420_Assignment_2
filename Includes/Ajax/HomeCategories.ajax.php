<?php

ini_set("display_errors","1");

include_once('../Session.php');
include_once("../CategoryManager.php");
include_once('../Common.php');

/*
	AJAX page for showing homp page categories
*/

// check for malformed AJAX
if (!isset($_REQUEST["p"]))
{
	// redirect to AJAX error page.
	$_SESSION["last_Error"] = "AJAX_Error";
	$_SESSION["Error_MSG"] = "Home Cart ajax page: ";
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
	// show current page of categories
	$categoryManager = new \BusinessLayer\CategoryManager;

	$page = (integer) ($_REQUEST["p"] + 0);
	
	$pagesize = \Common\Constants::$HomeCategoriesTablePageSize;
	
	if ($page < 1)
	{
		$page = 1;
	}
	
	$start = ($page - 1) * $pagesize;
	
	$categories = $categoryManager->RetrieveCategoriesForHomePage($start, $pagesize);
	
	// display each category.
	foreach($categories as $cat)
	{
		$id = $cat['id'];
		$name = $cat['name'];
		$imgUrl = '../' . \Common\Constants::$AdminFileuploadFolder .'/'. $cat["imageUrl"];
		
		echo '<div class="row"><div class="col-xs-0 col-sm-3 col-md-3"></div>'.
			'<div class="col-xs-12 col-sm-6 col-md-6"><img style="max-width:80%;max-height:80%" alt="no picture" src="'.$imgUrl.'" /></div></div>'.
			'<div class="row"><div class="col-xs-0 col-sm-3 col-md-3"></div>'.
			'<div class="col-xs-12 col-sm-6 col-md-6"><input type="button" value="'.$name.'" onclick="ShowPageCaps('.$id.',1)" /></div></div>'.
			'<br/>';
	}
}
