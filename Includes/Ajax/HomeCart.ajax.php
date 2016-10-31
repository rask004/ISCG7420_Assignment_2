<?php

ini_set("display_errors","1");

include_once('../Session.php');
include_once("../CapManager.php");
include_once('../Common.php');

/*

*/

// check for malformed AJAX
if (!isset($_REQUEST["c"]) && !isset($_REQUEST["p"]) && !isset($_REQUEST["d"]) && !isset($_REQUEST["a"]) && !isset($_REQUEST["aq"]) )
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

// clear the cart.
elseif (isset($_REQUEST["c"]))
{
	$_SESSION[\Common\Security::$SessionCartArrayKey] = array();
	
	echo '<p><label>There are no items in your shopping cart.</label></p>';
}

// delete one cart item.
elseif (isset($_REQUEST["d"]))
{
	$id = (integer) ($_REQUEST["d"] + 0);
	
	unset($_SESSION[\Common\Security::$SessionCartArrayKey][$id]);
}

// add a cart item, with a quantity to add by.
elseif( isset($_REQUEST["a"]) && isset($_REQUEST["aq"]) )
{
	$id = (integer) ($_REQUEST["a"] + 0);
	$qty = (integer) ($_REQUEST["aq"] + 0);
	
	if ( !isset($_SESSION[\Common\Security::$SessionCartArrayKey][$id]))
	{
		$_SESSION[\Common\Security::$SessionCartArrayKey][$id] = 0;	
	}
	
	$_SESSION[\Common\Security::$SessionCartArrayKey][$id] += $qty;
}

// update cart page
if (isset($_REQUEST["p"]))
{
	$cart = $_SESSION[\Common\Security::$SessionCartArrayKey];
	
	if (count($cart) == 0)
	{
		echo '<p><label>There are no items in your shopping cart.</label></p>';
	}
	else
	{
		$itemcount = count($cart);
		$page = (integer) ($_REQUEST["p"] + 0);
		$pagesize = \Common\Constants::$HomeCartTablePageSize;	
		$capsManager = new \BusinessLayer\CapManager;
		
		// cannot have a page of 0 or less.
		if ($page < 1)
		{
			$page = 1;
		}
		// if an item has been deleted, may need to go back one page.
		elseif(($page - 1) * $pagesize >= $itemcount)
		{
			$page -= 1;
		}
		
		// find start and end items of page.
		$start = (($page - 1) * \Common\Constants::$HomeCartTablePageSize) + 1;
		$end = ($page * \Common\Constants::$HomeCartTablePageSize);
		
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
			$price = number_format((float)$cap["price"], 2, '.', '');
			$name = $cap["name"];
			$total = $price * $qty;
			
			
			echo '<div class="row"><div class="col-xs-0 col-sm-1 col-md-1"></div>'.
				'<div class="col-xs-4 col-sm-2 col-md-2">'.
				'<input style="background-color:red" type="button" onclick="DeleteCartItem('.$capId.')" value="X" /></div>'.
				'<div class="col-xs-4 col-sm-2 col-md-2">'.
				'<label>ID: </label></div><div class="col-xs-4 col-sm-2 col-md-2">'.
				'<span>'. $capId .'</span></div><div class="col-xs-0 col-sm-4 col-md-4"></div></div>';
			echo '<div class="row"><div class="col-xs-0 col-sm-1 col-md-1"></div>'.
				'<div class="col-xs-4 col-sm-2 col-md-2"><label>Name: </label></div>'.
				'<div class="col-xs-8 col-sm-8 col-md-8"><span>'. $name .'</span></div></div>';
			echo '<div class="row"><div class="col-xs-0 col-sm-1 col-md-1"></div>'.
				'<div class="col-xs-4 col-sm-2 col-md-2"><label>Qty: </label></div><div class="col-xs-6 col-sm-1 col-md-1">'. $qty .'</div>'.
				'<div class="col-xs-4 col-sm-1 col-md-1"><label>X</label></div>'.
				'<div class="col-xs-8 col-sm-2 col-md-2"><span>$'. $price .'</span></div>'.
				'<div class="col-xs-4 col-sm-1 col-md-1"><label>=</label></div>'.
				'<div class="col-xs-8 col-sm-1 col-md-1"><span>$'. $total .'</span></div></div><br/>';
			
		}
		
		// if not enough items to fill the page, create empty placeholders.
		if (count( $page_items) < (\Common\Constants::$HomeCartTablePageSize) )
		{
			$c = (\Common\Constants::$HomeCartTablePageSize) - (count( $page_items));
			
			while ($c >0)
			{
				echo '<div class="row"><div class="col-xs-8 col-sm-8 col-md-8">&nbsp;</div></div>'.
					'<div class="row"><div class="col-xs-8 col-sm-8 col-md-8">&nbsp;</div></div>'.
					'<div class="row"><div class="col-xs-8 col-sm-8 col-md-8">&nbsp;</div></div><br/>';
			
				$c -= 1;	
			}
		}
	}
}