<?php

/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 17/10/2016
 * Time: 19:24 PM
 */
ini_set('display_errors', 1);

include_once('../Includes/Session.php');
include_once('../Includes/Common.php');


if (!(isset($_SESSION[\Common\Security::$SessionAuthenticationKey]) && $_SESSION[\Common\Security::$SessionAuthenticationKey] == 1
	&& isset($_SESSION[\Common\Security::$SessionAdminCheckKey])))
{
    header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/logout.php");
    exit;
}
?>


<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quality Caps - Admin Section</title>
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.structure.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.theme.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/Common.css">
    <script type="text/javascript" src="../js/jquery.js"></script>
</head>

<body>
    <?php include_once("../Includes/navbar.admin.php"); ?>

    <div class="container-fluid PageContainer">

        <div class="row">
            <div id="divLeftSidebar" class="col-md-1">
            </div>
            <div id="divCentreSpace" class="col-md-10">

                <form method="post" enctype="multipart/form-data">
                    <div class="container-fluid PageSection">
                        
                    </div>
                </form>

            </div>
            <div id="divRightsidebar" class="col-md-1">
            </div>
        </div>
    </div>

    <?php include_once("../Includes/footer.php"); ?>
</body>
</html>