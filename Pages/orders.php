<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 10:17 PM
 */

include_once('../Includes/Session.php');

// non-authenticated users should not be here.
if (!isset($_SESSION['IsAuthenticated']) || $_SESSION['IsAuthenticated'] != 1)
{
    header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/home.php");
    exit;
}

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quality Caps - Orders</title>
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.structure.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.theme.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/Common.css">
    <script type="text/javascript" src="../js/jquery.js"></script>
</head>

<body>

<?= 'This is the orders page' ?>
</body>
</html>

