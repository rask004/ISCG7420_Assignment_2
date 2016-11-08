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
	$_SESSION["Error_MSG"] = "Admin Caps ajax page: ";
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
	// add a cap
    $name = $_REQUEST["name"];
    $price = $_REQUEST["price"];
    $desc = $_REQUEST["description"];
    $imgUrl = $_REQUEST["imageUrl"];
    $supId = $_REQUEST["supplierId"];
    $catId = $_REQUEST["categoryId"];

    $Manager->AddCap($name, $price, $desc, $imgUrl, $supId, $catId);
}
else if (isset($_REQUEST["d"]))
{
    // delete a cap
    $id = $_REQUEST["id"];

    $Manager->DeleteCap($id);
}
else if (isset($_REQUEST["r"]))
{
    // remove the category of a cap
    $id = $_REQUEST["id"];

    $Manager->RetireCap($id);
}

// allows updating the category list to be simultaineous.
if (isset($_REQUEST["l"]))
{
    // update list of categories at left
    $Caps = $Manager->GetAllCaps();
    foreach($Caps as $cap)
    {
        echo '<div class="row"><div class="col-xs-12 col-sm-12 col-sm-12">';
        echo '<input class="btn" style="border:1px solid black; margin-bottom:2px; width:100%;" ' .
            'type="button" value="' . $cap['id'] . ', ' . $cap['name'] . '" ' .
            'data-name="'.$cap['name'].'" data-description="'.$cap['description'].'" ' .
            'data-price="'.$cap['price'].'" data-imageurl="'.$cap['imageUrl'].'" ' .
            'data-supplierid="'.$cap['supplierId'].'" ' .
            'data-categoryid="'.$cap['categoryId'].'" ' .
            'id="cap_' . $cap['id'] . '" ' .
            'onclick="UpdateItemForm(' . $cap['id'] . ')" /> ';
        echo '</div></div>';
    }
}
