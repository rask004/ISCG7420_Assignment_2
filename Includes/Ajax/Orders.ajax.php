<?php

include_once('../Session.php');
include_once("../OrderManager.php");
include_once('../Common.php');

 ini_set('display_errors','1');

// Check for correct parameters. redirect to ajax error page if malformed.
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
	$ordersManager = new \BusinessLayer\OrderManager;

	$page = (integer) ($_REQUEST["p"] + 0);
	
	if ($_REQUEST["p"] >= 2)
	{
		$page = $_REQUEST["p"];
	}
	else
	{
		$page = 1;
	}
	
	$start = ($page - 1) * \Common\Constants::$OrdersTablePageSize;
	$length = ($page * \Common\Constants::$OrdersTablePageSize);
	$id = $_SESSION[\Common\Security::$SessionUserIdKey];
	
	echo '<tr style="border-bottom: black solid 1px"><th>Id</th><th>Date Placed</th><th>Status</th><th>Total Items</th><th>Total Cost ($)</th></tr>';
	
	
	
	$order_summaries = $ordersManager->GetAllOrderSummariesForCustomer($id, $start, $length);
	
	foreach($order_summaries as $summary)
	{
		$date_parts = explode(" ", $summary['datePlaced']);
		echo "<tr><td>". $summary['id'] ."</td><td>". $date_parts[0] ."</td><td>". $summary['status'] .
		"</td><td>". $summary['totalQuantity'] ."</td><td>". $summary['totalPrice'] ."</td><td></tr>";
	}
}
