<?php

include_once('../Session.php');
include_once("../Common.php");
include_once('../OrderManager.php');

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
	
	$pagesize = \Common\Constants::$OrdersTablePageSize;
	
	if ($page < 1)
	{
		$page = 1;
	}
	
	$start = ($page - 1) * \Common\Constants::$OrdersTablePageSize;
	$id = $_SESSION[\Common\Security::$SessionUserIdKey];
	
	echo '<tr><th>Id</th><th>Date Placed</th><th>Status</th><th>Total Items</th><th>Total Cost ($)</th></tr>';
	
	$order_summaries = $ordersManager->GetAllOrderSummariesForCustomer($id, $start, $pagesize);
	
	foreach($order_summaries as $summary)
	{
		$date_parts = explode(" ", $summary['datePlaced']);
		echo "<tr><td>". $summary['id'] ."</td><td>". $date_parts[0] ."</td><td>". $summary['status'] .
		"</td><td>". $summary['totalQuantity'] ."</td><td>". number_format((float) $summary['totalPrice'], 2, '.', '') ."</td><td></tr>";
	}
	
	if( count($order_summaries) < $pagesize)
	{
		$c = $pagesize - count($order_summaries);
		
		while( $c > 0)
		{
			echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td></tr>";
			$c -= 1;	
		}
	}
	
}

?>
