<?php
/**
 * Created by PhpStorm.
 * User: Roland Askew, 1461498
 * Date: 11/10/2016
 * Time: 10:16 PM
 */

include_once('../Includes/Session.php');

// check timeout
checkTimeOut();
// no timeout - update the last access time.
$_SESSION[\Common\SecurityConstraints::$SessionTimestampLastVisit] = time();

include_once('../Includes/Common.php');

$customerId = "VISITOR";
if(isset($_SESSION[\Common\SecurityConstraints::$SessionUserIdKey]))
{
    $customerId = $_SESSION[\Common\SecurityConstraints::$SessionUserIdKey];
}

\Common\Logging::Log('Executing Page. sessionId=' . session_id() . '; customer='
    . $customerId . "\r\n");

if (isset($_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey]) && isset($_SESSION[\Common\SecurityConstraints::$SessionAdminCheckKey]))
{
    header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/AdminFiles.php");
    exit;
}

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quality Caps - Contact Us</title>
    <script type="text/javascript" src="../js/jquery.min.js"></script>
    <script type="text/javascript" src="../js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/Common.css">
</head>

<body>
    <?php
		// show correct navbar, depending on whether or not user is logged in.
        if (isset($_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey]) && $_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey] == 1)
        {
            include_once("../Includes/navbar.member.php");
        }
        else
        {
            include_once("../Includes/navbar.visitor.php");
        }
    ?>

    <div class="container-fluid PageContainer">

        <div class="row">
            <div id="divLeftSidebar" class="col-md-3">

            </div>
            <div id="divCentreSpace" class="col-md-6">
                <div class="container-fluid panel panel-default PageSection">
                    <br/>

                    <div class="row" style="margin: auto 20px">
                        <div class="col-xs-0 col-sm-4 col-md-3">
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-6 DecoHeader">
                            <H3>
                                Contact Us
                            </H3>
                        </div>
                        <div class="col-xs-0 col-sm-4 col-md-3">
                        </div>
                    </div>

                    <br/>
                    <br/>
                    
                    <!-- show contact details -->

                    <div class="row" style="margin-top: 4px">
                        <div class="col-xs-0 col-sm-1 col-md-2">

                        </div>
                        <div class="col-xs-12 col-sm-5 col-md-4">
                            <p style="float:left"><label>Phone Number:</label></p>
                        </div>
                        <div class="col-xs-12 col-sm-5 col-md-4">
                            <p style="float:left">99-5555-5555</p>
                        </div>
                        <div class="col-xs-0 col-sm-1 col-md-2">

                        </div>
                    </div>

                    <div class="row" style="margin-top: 4px">
                        <div class="col-xs-0 col-sm-1 col-md-2">

                        </div>
                        <div class="col-xs-12 col-sm-5 col-md-4">
                            <p style="float:left"></p>
                        </div>
                        <div class="col-xs-12 col-sm-5 col-md-4">
                            <p style="float:left">(9am to 5:30pm)</p>
                        </div>
                        <div class="col-xs-0 col-sm-1 col-md-2">

                        </div>
                    </div>

                    <br/>

                    <div class="row" style="margin-top: 4px">
                        <div class="col-xs-0 col-sm-1 col-md-2">

                        </div>
                        <div class="col-xs-12 col-sm-5 col-md-4">
                            <p style="float:left"><label>Fax Number:</label></p>
                        </div>
                        <div class="col-xs-12 col-sm-5 col-md-4">
                            <p style="float:left">99-5555-6666</p>
                        </div>
                        <div class="col-xs-0 col-sm-1 col-md-2">

                        </div>
                    </div>

                    <br/>

                    <div class="row" style="margin-top: 4px">
                        <div class="col-xs-0 col-sm-1 col-md-2">

                        </div>
                        <div class="col-xs-12 col-sm-5 col-md-4">
                            <p style="float:left"><label>Sales:</label></p>
                        </div>
                        <div class="col-xs-12 col-sm-5 col-md-4">
                            <p style="float:left">sales@QualityCaps.co.nz</p>
                        </div>
                        <div class="col-xs-0 col-sm-1 col-md-2">

                        </div>
                    </div>

                    <div class="row" style="margin-top: 4px">
                        <div class="col-xs-0 col-sm-1 col-md-2">

                        </div>
                        <div class="col-xs-12 col-sm-5 col-md-4">
                            <p style="float:left"><label>General:</label></p>
                        </div>
                        <div class="col-xs-12 col-sm-5 col-md-4">
                            <p style="float:left">general@QualityCaps.co.nz</p>
                        </div>
                        <div class="col-xs-0 col-sm-1 col-md-2">

                        </div>
                    </div>

                    <div class="row" style="margin-top: 4px">
                        <div class="col-xs-0 col-sm-1 col-md-2">

                        </div>
                        <div class="col-xs-12 col-sm-5 col-md-4">
                            <p style="float:left"><label>IT Support:</label></p>
                        </div>
                        <div class="col-xs-12 col-sm-5 col-md-4">
                            <p style="float:left">support@QualityCaps.co.nz</p>
                        </div>
                        <div class="col-xs-0 col-sm-1 col-md-2">

                        </div>
                    </div>

                    <br/>

                    <div class="row" style="margin-top: 4px">
                        <div class="col-xs-0 col-sm-1 col-md-2">

                        </div>
                        <div class="col-xs-12 col-sm-5 col-md-4">
                            <p style="float:left"><label>Mailing Address:</label></p>
                        </div>
                        <div class="col-xs-12 col-sm-5 col-md-4">
                            <p style="float:left">PO Box 7711</p>
                        </div>
                        <div class="col-xs-0 col-sm-1 col-md-2">

                        </div>
                    </div>

                    <div class="row" style="margin-top: 4px">
                        <div class="col-xs-0 col-sm-1 col-md-2">

                        </div>
                        <div class="col-xs-12 col-sm-5 col-md-4">
                            <p style="float:left"></p>
                        </div>
                        <div class="col-xs-12 col-sm-5 col-md-4">
                            <p style="float:left">44 Simon Says Street</p>
                        </div>
                        <div class="col-xs-0 col-sm-1 col-md-2">

                        </div>
                    </div>

                    <div class="row" style="margin-top: 4px">
                        <div class="col-xs-0 col-sm-1 col-md-2">

                        </div>
                        <div class="col-xs-12 col-sm-5 col-md-4">
                            <p style="float:left"></p>
                        </div>
                        <div class="col-xs-12 col-sm-5 col-md-4">
                            <p style="float:left">Sunny Side Suburb</p>
                        </div>
                        <div class="col-xs-0 col-sm-1 col-md-2">

                        </div>
                    </div>

                    <div class="row" style="margin-top: 4px">
                        <div class="col-xs-0 col-sm-1 col-md-2">

                        </div>
                        <div class="col-xs-12 col-sm-5 col-md-4">
                            <p style="float:left"></p>
                        </div>
                        <div class="col-xs-12 col-sm-5 col-md-4">
                            <p style="float:left">Auckland</p>
                        </div>
                        <div class="col-xs-0 col-sm-1 col-md-2">

                        </div>
                    </div>

                    <br/>

                    <div class="row" style="margin-top: 4px">
                        <div class="col-xs-0 col-sm-1 col-md-2">

                        </div>
                        <div class="col-xs-12 col-sm-5 col-md-4">
                            <p style="float:left"><label>Postcode:</label></p>
                        </div>
                        <div class="col-xs-12 col-sm-5 col-md-4">
                            <p style="float:left">9999</p>
                        </div>
                        <div class="col-xs-0 col-sm-1 col-md-2">

                        </div>
                    </div>

                    <br/>

                    <div class="row" style="margin-top: 4px">
                        <div class="col-xs-0 col-sm-1 col-md-2">

                        </div>
                        <div class="col-xs-12 col-sm-5 col-md-4">
                            <p style="float:left"><label>Country:</label></p>
                        </div>
                        <div class="col-xs-12 col-sm-5 col-md-4">
                            <p style="float:left">New Zealand</p>
                        </div>
                        <div class="col-xs-0 col-sm-1 col-md-2">

                        </div>
                    </div>

                    <br/>

                </div>
            </div>
            <div id="divRightsidebar" class="col-md-3">
            </div>
        </div>
    </div>


    <?php include_once("../Includes/footer.php"); ?>
</body>
</html>

