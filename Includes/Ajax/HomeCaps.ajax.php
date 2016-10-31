<?php

ini_set("display_errors","1");

include_once('../Session.php');
include_once("../CapManager.php");
include_once('../Common.php');

/*
	AJAX page for showing home page caps
*/

// check for malformed AJAX
if (isset($_REQUEST["p"]) && isset($_REQUEST["c"]) )
{
	// ok to process
	
	$CapManager = new \BusinessLayer\CapManager;
	$categoryId = (integer) ($_REQUEST["c"] + 0);
	$page = (integer) ($_REQUEST["p"] + 0);
	$pagesize = \Common\Constants::$HomeCapsTablePageSize;
	$pagewidth = \Common\Constants::$HomeCapsTablePageWidth;
	
	if ($page < 1)
	{
		$page = 1;
	}
	
	$start = ($page - 1) * $pagesize;
	
	$caps = array();
	
	$capCount = 0;
	
	if ($categoryId < 0)
	{
		$caps = $CapManager->GetAllCaps($start, $pagesize);
		$capCount = $CapManager->GetAllCapsCount();
	}
	else
	{
		$caps = $CapManager->GetCapsByCategorywithLimit($categoryId, $start, $pagesize);
		$capCount = $CapManager->GetCapsByCategoryCount($categoryId);
	}
	
	echo '<script type="text/javascript">$("#inputJsParamsCapItemCount").val('.$capCount.')</script>';
	
	$c = 0;
	
	$colWidth = 12 / $pagewidth;
	
	echo '<div class="container-fluid"><div class="row">';
	
	foreach($caps as $cap)
	{
		if ($c >= $pagewidth)
		{
			echo '</div><br/><div class="row">';
			$c = 0;
		}
		
		$imgUrl = '../' . \Common\Constants::$AdminFileuploadFolder .'/'. $cap["imageUrl"];
		$name = $cap["name"];
		$price = number_format((float)$cap["price"], 2, '.', '');
		$id = $cap["id"];
		
		echo '<div class="col-xs-12 col-sm-'.$colWidth.' col-md-'.$colWidth.'"><div class="container-fluid">';
		
		echo '<div class="row"><div class="col-xs-12 col-sm-12 col-md-12">'.
			'<img style="width:160px; height:160px;" class="img-thumbnail" alt="no picture" src="'.$imgUrl.'" /></div></div><br/>'.
			'<div class="row"><div class="col-xs-8 col-sm-8 col-md-8">'.
			'<input type="button" class="btn btn-primary" value="'.$name.'" onclick="ShowCapDetails('.$id.');" /></div>'.
			'<div class="col-xs-0 col-sm-0 col-md-1"></div>'.
			'<div class="col-xs-12 col-sm-8 col-md-8"><label>$ '.$price.' </label></div></div>';
			
		echo '</div></div>';
		
		// limit cells per row to page width.	
		$c += 1;
	}
	
	echo '</div></div>';
}
// requesting to show details of specific cap
elseif (isset( $_REQUEST["d"] ))
{
	$capId = (integer) ($_REQUEST["d"] + 0);
	
	$CapManager = new \BusinessLayer\CapManager;
	
	$cap = $CapManager->GetSingleCap($capId);
	
	if (!empty($cap))
	{
		$id = $cap["id"];
		$name = $cap["name"];
		$price = number_format((float)$cap["price"], 2, '.', '');
		$imgUrl = '../' . \Common\Constants::$AdminFileuploadFolder .'/'. $cap["imageUrl"];
		$description = $cap["description"];
			
		echo '<div class="row"><div class="col-xs-0 col-sm-2 col-md-2"></div><div class="col-xs-12 col-sm-10 col-md-10">'.
        	'<img style="max-width:50%" class="img-thumbnail" class="img-thumbnail" id="imgCapDetails" src="'.$imgUrl.'" alt="NO IMAGE" />'.
			'</div></div><br/>'.
        	'<div class="row"><div class="col-xs-0 col-sm-2 col-md-2"></div>'.
			'<div class="col-xs-6 col-sm-1 col-md-1"><label>ID:</label></div>'.
			'<div class="col-xs-0 col-sm-1 col-md-1"></div>'.
			'<div class="col-xs-6 col-sm-6 col-md-6"><label id="lblAddCapId">'.$id.'</label></div></div>'.
			'<div class="row"><div class="col-xs-0 col-sm-2 col-md-2"></div>'.
			'<div class="col-xs-6 col-sm-1 col-md-1"><label>Name:</label></div>'.
			'<div class="col-xs-0 col-sm-1 col-md-1"></div>'.
			'<div class="col-xs-6 col-sm-6 col-md-6"><label>'.$name.'</label></div></div>'.
			'<div class="row"><div class="col-xs-0 col-sm-2 col-md-2"></div>'.
			'<div class="col-xs-6 col-sm-1 col-md-1"><label>Price:</label></div>'.
			'<div class="col-xs-0 col-sm-1 col-md-1"></div>'.
			'<div class="col-xs-6 col-sm-6 col-md-6"><label>$'.$price.'</label></div></div>'.
			'<div class="row"><div class="col-xs-0 col-sm-2 col-md-2"></div>'.
			'<div class="col-xs-6 col-sm-1 col-md-1"><label>Quantity:</label></div>'.
			'<div class="col-xs-0 col-sm-1 col-md-1"></div>'.
			'<div class="col-xs-6 col-sm-6 col-md-6"><input type="number" id="inputAddCapQuantity" min="1" max="9" value="1"/></div></div><br/>'.
			'<div class="row"><div class="col-xs-0 col-sm-2 col-md-2"></div>'.
			'<div class="col-xs-6 col-sm-9 col-md-9">'.$description.'</div></div><br/>'.
			'<div class="row"><div class="col-xs-0 col-sm-2 col-md-2"></div>'.
			'<div class="col-xs-6 col-sm-3 col-md-3"><input type="button" class="btn btn-primary" value="Add To Cart" onclick="AddCapToCart()" /></div>'.
			'<div class="col-xs-0 col-sm-2 col-md-2"></div>'.
			'<div class="col-xs-6 col-sm-4 col-md-4"><input type="button" class="btn btn-primary" value="Cancel" onclick="ReturnToCapListing()" /></div>';
	}
}
else
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