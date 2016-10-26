<?php

ini_set("display_errors","1");

include_once('../Session.php');
include_once("../CapManager.php");
include_once('../Common.php');

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
	$cart = $_SESSION[\Common\Security::$SessionCartArrayKey];
	
	if (count($cart) == 0)
	{
		echo '<div class="row"><label>There are no items in your shopping cart.</label></div>';
	}
	else
	{
		$capsManager = new \BusinessLayer\CapManager;
	
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
		$page_items = array();
	
		foreach($cart as $capId=>$qty)
		{
			// if item is in current page, store it.
			if($c >= $start && $c <= $end)
			{
				$page_items[$capId] = $qty;	
			}
			
			$c += 1;
			if ($c > $end)
			{
				break;	
			}
		}
	
		// now display the pages
		foreach($page_items as $capId=>$qty)
		{
			$cap = $capsManager->GetSingleCap($capId);
			$price = $cap["price"];
			$name = $cap["name"];
			$total = $price * $qty;
			
			
			echo '<div class="row"><div class="col-xs-0 col-sm-1 col-md-1"></div>'.
				'<div class="col-xs-6 col-sm-2 col-md-2">'.
				'<input style="background-color:red" type="button" value="X" onclick="$.when(CheckoutItemDelete('.$capId .')).then(CheckoutAjaxPage(1))" /></div>'.
				'<div class="col-xs-3 col-sm-2 col-md-2"><label>ID: </label></div><div class="col-xs-3 col-sm-7 col-md-7">'.
				'<span>'. $capId .'</span></div></div>';
			echo '<div class="row"><div class="col-xs-0 col-sm-3 col-md-3"></div>'.
				'<div class="col-xs-4 col-sm-2 col-md-2"><label>Name: </label></div>'.
				'<div class="col-xs-8 col-sm-7 col-md-7"><span>'. $name .'</span></div>'.
				'<div class="col-xs-0 col-sm-1 col-md-1"></div></div>';
			echo '<div class="row"><div class="col-xs-0 col-sm-3 col-md-3"></div>'.
				'<div class="col-xs-6 col-sm-1 col-md-1"><label>Qty: </label></div><div class="col-xs-6 col-sm-1 col-md-1"><span>'. $qty .'</span></div>'.
				'<div class="col-xs-6 col-sm-1 col-md-1"><label>X</label></div>'.
				'<div class="col-xs-6 col-sm-2 col-md-2"><span>$'. $price .'</span></div>'.
				'<div class="col-xs-6 col-sm-1 col-md-1"><label>=</label></div>'.
				'<div class="col-xs-6 col-sm-1 col-md-1"><span>$'. $total .'</span></div></div><br/>';
			
		}
	}
}
