<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 10:09 PM
 */

include_once('../Includes/Session.php');

// process postback - for testing, currently expect a particular user and pass (not real user).
if (isset($_POST['inputLogin']) && isset($_POST['inputPassword'])
    && $_POST['inputLogin'] == 'test__login' && $_POST['inputPassword'] == 'test__password')
{
    $_SESSION['IsAuthenticated'] = 1;
    $_SESSION['CustomerLogin']  = 'test__login';
    $_SESSION['CustomerID'] = 1;
}

//  redirect already authenticated users - redirect to home.
if (isset($_SESSION['IsAuthenticated']) && $_SESSION['IsAuthenticated'] == 1)
{
    header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/home.php");
    exit;
}

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quality Caps - Login</title>
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.structure.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.theme.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/Common.css">
    <script type="text/javascript" src="../js/jquery.js"></script>
</head>

<body>
    <?php include_once ("../Includes/navbar.html.php"); ?>

    <div class="container-fluid PageContainer">

        <div class="row">
            <div id="divLeftSidebar" class="col-md-3">

            </div>
            <div id="divCentreSpace" class="col-md-6">

                <form method="post" enctype="multipart/form-data">
                    <div class="container-fluid PageSection">
                        <br/>

                        <div class="row" style="margin: auto 20px">
                            <div class="col-xs-0 col-sm-4 col-md-3">
                            </div>
                            <div class="col-xs-12 col-sm-4 col-md-6 DecoHeader">
                                <H3>
                                    Login
                                </H3>
                            </div>
                            <div class="col-xs-0 col-sm-4 col-md-3">
                            </div>
                        </div>

                        <br/>
                        <br/>

                        <div class="row">
                            <div class="col-xs-0 col-sm-2 col-md-2">
                            </div>
                            <div class="col-xs-12 col-sm-8 col-md-8">
                                <div class="row" style="margin-top: 4px">
                                    <div class="col-xs-12 col-sm-4 col-md-5">
                                        <label style="float: left" for="inputLogin">Login:</label>
                                    </div>
                                    <div class="col-xs-12 col-sm-8 col-md-7">
                                        <input style="float: right; width:100%" id="inputLogin"
                                               name="inputLogin" required type="text" />
                                    </div>
                                </div>
                                <div class="row" style="margin-top: 4px">
                                    <div class="col-xs-12 col-sm-4 col-md-5">
                                        <label style="float: left" for="inputPassword">Password:</label>
                                    </div>
                                    <div class="col-xs-12 col-sm-8 col-md-7">
                                        <input style="float: right; width:100%" id="inputPassword"
                                               name="inputPassword" required minlength="10" type="text" />
                                    </div>
                                </div>

                                <br/>

                                <div class="row" style="margin-top: 4px">
                                    <div class="col-xs-2 col-sm-3 col-md-3">

                                    </div>
                                    <div class="col-xs-8 col-sm-6 col-md-6">
                                        <input style="width:100%" type="button" value="Reset Password" />
                                    </div>
                                    <div class="col-xs-2 col-sm-3 col-md-3">

                                    </div>
                                </div>

                                <br/>

                                <div class="row" style="margin-top: 4px">
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <input style="width:100%" type="reset" value="Clear" />
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">

                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <input style="width:100%" name="submit" type="submit" value="Submit" />
                                    </div>
                                </div>

                                <br/>

                            </div>
                            <div class="col-xs-0 col-sm-2 col-md-2">
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            <div id="divRightsidebar" class="col-md-3">
                <br/>
                <br/>
                <p><?php print_r($_SESSION); ?></p>
            </div>
        </div>
    </div>

    <?php include_once ("../Includes/footer.html.php"); ?>
</body>
</html>

