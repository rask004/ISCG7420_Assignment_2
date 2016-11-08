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
	// add a category
    $name = $_REQUEST["name"];

    $Manager->AddCategory($name);
}
else if (isset($_REQUEST["d"]))
{
    // delete a category
    $id = $_REQUEST["id"];

    $Manager->DeleteCategory($id);
}

// allows updating the category list to be simultaineous.
if (isset($_REQUEST["l"]))
{
    // update list of categories at left
    $Categories = $Manager->GetAllCategories();
    foreach($Categories as $category)
    {
        echo '<div class="row"><div class="col-xs-12 col-sm-12 col-sm-12">';
        echo '<input class="btn" style="border:1px solid black; margin-bottom:2px; width:80%;" ' .
            'type="button" value="' . $category['id'] . ', ' . $category['name'] . '" ' .
            'data-name="'.$category['name'].'" id="category_' . $category['id'] . '" ' .
            'onclick="UpdateItemForm(' . $category['id'] . ')" /> ';
        echo '</div></div>';
    }
}
