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
	$_SESSION["Error_MSG"] = "Admin Customers ajax page: ";
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
    $firstName = $_REQUEST["firstname"];
    $lastName = $_REQUEST["lastname"];
    $login = $_REQUEST["login"];
    $password = $_REQUEST["password"];
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

    $address = $_REQUEST["address"];
    $suburb = $_REQUEST["suburb"];
    $city = $_REQUEST["city"];

    $Manager->AddCustomer($firstName, $lastName, $login, $password, $email, $homeNumber,
        $workNumber, $mobileNumber, $address, $suburb, $city);
}
else if (isset($_REQUEST["d"]))
{
    // delete a user
    $id = $_REQUEST["id"];
    $Manager->DeleteCustomer($id);

}
else if (isset($_REQUEST["x"]))
{
    // disable a user
    $id = $_REQUEST["id"];
    $Manager->DisableCustomer($id);

}

// allows updating the user list to be simultaineous.
if (isset($_REQUEST["l"]))
{
    // update list of users at left
    $Users = $Manager->GetAllCustomers();
    foreach($Users as $user)
    {
        echo '<div class="row"><div class="col-xs-12 col-sm-12 col-sm-12">';
        echo '<input class="btn" style="border:1px solid black; margin-bottom:2px; width:80%;" ' .
            'type="button" value="' . $user['id'] . ', ' . $user['login'] . '" ' .
            'data-firstname="'.$user['firstName'].'" data-lastname="'.$user['lastName'].'" ' .
            'data-login="'.$user['login'].'" ' .
            'data-homenumber="'.$user['homeNumber'].'"' .
            'data-worknumber="'.$user['workNumber'].'" data-email="'.$user['emailAddress'].'"' .
            'data-address="'.$user['streetAddress'].'"' .
            'data-suburb="'.$user['suburb'].'" data-city="'.$user['city'].'"' .
            'data-mobilenumber="'.$user['mobileNumber'].'"' .
            'data-disabled="'.$user['isDisabled'].'"' .
            'id="user_' . $user['id'] . '" ' .
            'onclick="UpdateItemForm(' . $user['id'] . ')" /> ';
        echo '</div></div>';
    }
}
