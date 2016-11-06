<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 11:08 PM
 */

include_once('../Includes/Session.php');

$customerId = "VISITOR";
if(isset($_SESSION[\Common\SecurityConstraints::$SessionUserIdKey]))
{
    $customerId = $_SESSION[\Common\SecurityConstraints::$SessionUserIdKey];
}

\Common\Logging::Log('Executing Page. sessionId=' . session_id() . '; customer='
    . $customerId . "\r\n");

// perform logout session actions here
unset($_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey]);
unset($_SESSION[\Common\SecurityConstraints::$SessionUserLoginKey]);
unset($_SESSION[\Common\SecurityConstraints::$SessionUserIdKey]);
unset($_SESSION[\Common\SecurityConstraints::$SessionAdminCheckKey]);

// Now any restart of session will involve new session id. This is more secure.
session_regenerate_id();

// don't allow caching of the logout page.
header("Cache-Control: no-cache");
header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/home.php");
exit;
