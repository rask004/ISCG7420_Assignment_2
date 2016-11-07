<?php

/**
 * Created by Dreamweaver.
 * User: Roland
 * Date: 28/10/2016
 * Time: 8:00 PM
 *
 *  Ajax page for Orders.
 */

include_once('../Session.php');
include_once("../Common.php");
include_once('../AdminManager.php');

use \BusinessLayer\AdminManager;

$customerId = "VISITOR";
if(isset($_SESSION[\Common\SecurityConstraints::$SessionUserIdKey]))
{
	$customerId = $_SESSION[\Common\SecurityConstraints::$SessionUserIdKey];
}

\Common\Logging::Log('Executing Ajax. sessionId=' . session_id() . '; customer='
    . $customerId . "\r\n");

$Manager = new AdminManager();

// Check for correct parameters. redirect to ajax error page if malformed.
if (!isset($_REQUEST["l"]) && !isset($_REQUEST["a"]) && !isset($_REQUEST["d"]))
{
	// redirect to AJAX error page.
	$_SESSION["last_Error"] = "AJAX_Error";
	$_SESSION["Error_MSG"] = "Admin Categories ajax page: ";
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

if (isset($_REQUEST["a"]))
{
	// add a supplier
    $name = $_REQUEST["name"];
    $email = $_REQUEST["email"];
    if (isset($_REQUEST["homeNumber"]))
    {
        $homeNumber = $_REQUEST["homeNumber"];
    }
    else
    {
        $homeNumber = '';
    }
    if (isset($_REQUEST["workNumber"]))
    {
        $workNumber = $_REQUEST["workNumber"];
    }
    else
    {
        $workNumber = '';
    }
    if (isset($_REQUEST["mobileNumber"]))
    {
        $mobileNumber = $_REQUEST["mobileNumber"];
    }
    else
    {
        $mobileNumber = '';
    }

    $Manager->AddSupplier($name, $email, $homeNumber, $workNumber, $mobileNumber);
}
else if (isset($_REQUEST["d"]))
{
    // delete a supplier
    $id = $_REQUEST["id"];

    $Manager->DeleteSupplier($id);
}

// allows updating the category list to be simultaineous.
if (isset($_REQUEST["l"]))
{
    // update list of categories at left
    $Suppliers = $Manager->GetAllSuppliers();
    foreach($Suppliers as $suppliers)
    {
        echo '<div class="row"><div class="col-xs-12 col-sm-12 col-sm-12">';
        echo '<input class="btn" style="border:1px solid black; margin-bottom:2px; width:80%;" ' .
            'type="button" value="' . $suppliers['id'] . ', ' . $suppliers['name'] . '" ' .
            'data-name="'.$suppliers['name'].'" data-homeNumber="'.$suppliers['homeNumber'].'"' .
            'data-workNumber="'.$suppliers['worknumber'].'" data-email="'.$suppliers['emailAddress'].'"' .
            'data-mobileNumber="'.$suppliers['mobileNumber'].'"' .
            'id="supplier_' . $suppliers['id'] . '" ' .
            'onclick="UpdateItemForm(' . $suppliers['id'] . ')" /> ';
        echo '</div></div>';
    }
}
