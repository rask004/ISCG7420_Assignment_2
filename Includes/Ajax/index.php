<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 9:40 PM
 */

include_once("../Common.php");

$customerId = "VISITOR";
if(isset($_SESSION[\Common\SecurityConstraints::$SessionUserIdKey]))
{
    $customerId = $_SESSION[\Common\SecurityConstraints::$SessionUserIdKey];
}

\Common\Logging::Log('Pages', 'Page /Pages/checkout.php accessed. sessionId=' . session_id() . '; customer='
    . $customerId . "; queryString=" . $_SERVER['QUERY_STRING'] . "\r\n");

// prevent visitors accessing this folder index.
header("Cache-Control: no-cache");
header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/home.php");
exit;


