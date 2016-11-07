<?php

/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 17/10/2016
 * Time: 19:24 PM
 */

include_once('../Includes/Session.php');
include_once('../Includes/Common.php');
include_once('../Includes/CategoryManager.php');

// check and log if this is an Admin
$customerId = "UNKNOWN";
$adminAccess = "FALSE";
if(isset($_SESSION[\Common\SecurityConstraints::$SessionUserIdKey]))
{
	$customerId = $_SESSION[\Common\SecurityConstraints::$SessionUserIdKey];
}
if(isset($_SESSION[\Common\SecurityConstraints::$SessionAdminCheckKey]))
{
    $adminAccess = "TRUE";
}

\Common\Logging::Log('Executing Page. sessionId=' . session_id() . '; customer='
	. $customerId . "; is_admin=" . $adminAccess."\r\n");

// redirect to home if this is not an Admin user.
if (!(isset($_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey]) && $_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey] == 1
	&& isset($_SESSION[\Common\SecurityConstraints::$SessionAdminCheckKey])))
{
    header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/logout.php");
    exit;
}
?>

<?php
    // manage forms
	if (isset($_POST["submit"]) )
	{

	}
?>


<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quality Caps - Admin Section</title>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/Common.css">
    <script type="text/javascript" src="../js/jquery.min.js"></script>
    <script type="text/javascript">
        // ajax calls
	</script>
</head>

<body>
    <?php include_once("../Includes/navbar.admin.php"); ?>

    <div class="container-fluid PageContainer" style="overflow-y:scroll; overflow-x:scroll">

        <div class="row">
            <div id="divLeftSidebar" class="col-xs-12 col-sm-3 col-md-3">
            	
            </div>
            <div id="divCentreSpace" class="col-xs-12 col-sm-9 col-md-9">

                <form method="post" enctype="multipart/form-data">
                    <div class="container-fluid PageSection">

                    </div>
                </form>

            </div>
        </div>
    </div>
    <br/>
    <br/>

    <?php include_once("../Includes/footer.php"); ?>
</body>
</html>