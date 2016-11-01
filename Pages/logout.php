<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 11:08 PM
 */

include_once('../Includes/Session.php');

// perform logout session actions here
unset($_SESSION[\Common\Security::$SessionAuthenticationKey]);
unset($_SESSION[\Common\Security::$SessionUserLoginKey]);
unset($_SESSION[\Common\Security::$SessionUserIdKey]);
unset($_SESSION[\Common\Security::$SessionAdminCheckKey]);

// Now any restart of session will involve new session id. This is more secure.
session_regenerate_id();

// don't allow caching of the logout page.
header("Cache-Control: no-cache");
header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/home.php");
exit;
