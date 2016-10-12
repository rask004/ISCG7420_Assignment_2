<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 11:08 PM
 */

// perform logout session actions here
$_SESSION['IsAuthenticated'] = 0;
unset($_SESSION['CustomerLogin']);
unset($_SESSION['CustomerID']);

header("Cache-Control: no-cache");
header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/home.php");
exit;
