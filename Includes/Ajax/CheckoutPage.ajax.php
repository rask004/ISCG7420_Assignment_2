<?php

/**
 * Created by Dreamweaver.
 * User: Roland
 * Date: 26/10/2016
 * Time: 8:00 PM
 *
 *  AJAX page for checkouts.  
 */

include_once('../Session.php');
include_once("../CapManager.php");
include_once('../Common.php');

$customerId = "VISITOR";
if(isset($_SESSION[\Common\SecurityConstraints::$SessionUserIdKey]))
{
    $customerId = $_SESSION[\Common\SecurityConstraints::$SessionUserIdKey];
}

\Common\Logging::Log('Pages', 'Page /Pages/checkout.php accessed. sessionId=' . session_id() . '; customer='
    . $customerId . "; queryString=" . $_SERVER['QUERY_STRING'] . "\r\n");

if (!isset($_REQUEST["p"]))
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
	// assume there are items in the cart to show.
	
	$cart = $_SESSION[\Common\SecurityConstraints::$SessionCartArrayKey];
	
	$CapsManager = new \BusinessLayer\CapManager;

	$page = (integer) ($_REQUEST["p"] + 0);
	
	if ($page < 1)
	{
		$page = 1;
	}
	elseif($page  > ((count($cart) / (\Common\Constants::$CheckoutTablePageSize)) + 1) )
	{
		$page = ((count($cart) / (\Common\Constants::$CheckoutTablePageSize)) + 1);
	}
	
	// find start and end items of page.
	$start = (($page -1) * \Common\Constants::$CheckoutTablePageSize) + 1;
	$end = ($page * \Common\Constants::$CheckoutTablePageSize);
	
	// counter for items searched
	$c = 1;
	
	// store pages to show.
	$pageItems = array();

	foreach($cart as $capId=>$qty)
	{
		// if item is in current page, store it.
		if($c >= $start && $c <= $end)
		{
			$pageItems[$capId] = $qty;	
		}
		
		$c += 1;
		if ($c > $end)
		{
			break;	
		}
	}

	// now display the pages
	foreach($pageItems as $capId=>$qty)
	{
		$cap = $CapsManager->GetSingleCap($capId);
		$price = number_format((float)$cap["price"], 2, '.', '');
		$name = $cap["name"];
		$total = number_format((float) ($price * $qty), 2, '.', '');
		
		
		echo '<div class="row"><div class="col-xs-0 col-sm-1 col-md-1"></div>'.
			'<div class="col-xs-2 col-sm-2 col-md-2">'.
			'<form method="post" enctype="multipart/form-data" autocomplete="off"><input class="btn btn-danger" type="submit" value="X" />'.
			'<input hidden type="text" value="'. $capId .'" name="CapId" />'.
			'<input hidden type="text" value="Delete" name="submit" /></form></div>'.
			'<div class="col-xs-4 col-sm-2 col-md-2">'.
			'<label>ID: </label></div><div class="col-xs-4 col-sm-7 col-md-7">'.
			'<span>'. $capId .'</span></div></div>';
		echo '<div class="row"><div class="col-xs-0 col-sm-3 col-md-3"></div>'.
			'<div class="col-xs-4 col-sm-2 col-md-2"><label>Name: </label></div>'.
			'<div class="col-xs-4 col-sm-7 col-md-7"><span>'. $name .'</span></div>'.
			'</div>';
		echo '<div class="row">'.
			'<div class="col-xs-0 col-sm-3 col-md-3"></div>'.
			'<div class="col-xs-2 col-sm-2 col-md-2"><label>Qty: </label></div><div class="col-xs-2 col-sm-1 col-md-1"><span>'. $qty .'</span></div>'.
			'<div class="col-xs-2 col-sm-1 col-md-1"><label>X</label></div>'.
			'<div class="col-xs-6 col-sm-2 col-md-2"><span>$'. $price .'</span></div>'.
			'<div class="col-xs-2 col-sm-1 col-md-1"><label>=</label></div>'.
			'<div class="col-xs-4 col-sm-1 col-md-1"><span>$'. $total .'</span></div></div><br/>';
		
	}
}
