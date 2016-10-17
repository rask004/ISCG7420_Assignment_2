<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 10:13 PM
 */

include_once('../Includes/Session.php');
include('../Includes/Common.php');

if (isset($_SESSION[\Common\Security::$SessionAuthenticationKey]) && $_SESSION[\Common\Security::$SessionAuthenticationKey] == 1)
{
    $isDisabled = 'disabled';
}
else
{
    $isDisabled = '';
}

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quality Caps -
        <?php
        if (isset($_SESSION[\Common\Security::$SessionAuthenticationKey]) && $_SESSION[\Common\Security::$SessionAuthenticationKey] == 1)
        {
            echo 'Profile';
        }
        else
        {
            echo 'Register';
        }
        ?>
    </title>
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.structure.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.theme.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/Common.css">
    <script type="text/javascript" src="../js/jquery.js"></script>
    <script type="text/javascript">
	
		function make_form_editable() 
		{
				$("#txtFirstName").prop( 'disabled', false);
				$("#txtLastName").prop( 'disabled', false);
				$("#txtLogin").prop( 'disabled', false);
				$("#txtPassword").prop( 'disabled', false);
				$("#txtHomePhone").prop( 'disabled', false);
				$("#txtWorkPhone").prop( 'disabled', false);
				$("#txtMobilePhone").prop( 'disabled', false);
				$("#txtAddress").prop( 'disabled', false);
				$("#txtSuburb").prop( 'disabled', false);
				$("#txtCity").prop( 'disabled', false);
				$("#submit").prop( 'disabled', false);
				$("#resetForm").prop( 'hidden', false);
				$("#btnEditForm").prop( 'hidden', true);
		}


		function reset_form() 
		{
				$("#txtFirstName").prop( 'disabled', true);
				$("#txtLastName").prop( 'disabled', true);
				$("#txtLogin").prop( 'disabled', true);
				$("#txtPassword").prop( 'disabled', true);
				$("#txtHomePhone").prop( 'disabled', true);
				$("#txtWorkPhone").prop( 'disabled', true);
				$("#txtMobilePhone").prop( 'disabled', true);
				$("#txtAddress").prop( 'disabled', true);
				$("#txtSuburb").prop( 'disabled', true);
				$("#txtCity").prop( 'disabled', true);
				$("#submit").prop( 'disabled', true);
				$("#resetForm").prop( 'hidden', true);
				$("#btnEditForm").prop( 'hidden', false);
		}
			
    </script>
</head>

<body>
    <?php
    if (isset($_SESSION[\Common\Security::$SessionAuthenticationKey]) && $_SESSION[\Common\Security::$SessionAuthenticationKey] == 1)
    {
        include_once("../Includes/navbar.member.php");
    }
    else
    {
        include_once("../Includes/navbar.visitor.php");
    }
    ?>

    <form method="post" enctype="multipart/form-data" autocomplete="off">

        <div class="container-fluid PageContainer">

            <div class="row">
                <div id="divLeftSidebar" class="col-md-3">

                </div>
                <div id="divCentreSpace" class="col-md-6">
                    <div class="container-fluid PageSection">
                        <br/>

                        <div class="row" style="margin: auto 20px">
                            <div class="col-xs-0 col-sm-4 col-md-3">
                            </div>
                            <div class="col-xs-12 col-sm-4 col-md-6 DecoHeader">
                                <H3>
                                    <?php
                                        if (isset($_SESSION[\Common\Security::$SessionAuthenticationKey]) && $_SESSION[\Common\Security::$SessionAuthenticationKey] == 1)
                                        {
                                            echo 'Profile';
                                        }
                                        else
                                        {
                                            echo 'Register';
                                        }
                                    ?>
                                </H3>
                            </div>
                            <div class="col-xs-0 col-sm-4 col-md-3">
                            </div>
                        </div>

                        <br/>
                        <br/>

                        <div class="row" style="margin-top: 4px">
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                            <div class="col-xs-12 col-sm-4 col-md-4">
                                <label style="float: left" for="txtFirstName">First Name:</label>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <?php
                                if (isset($_SESSION[\Common\Security::$SessionAuthenticationKey]) && $_SESSION[\Common\Security::$SessionAuthenticationKey] == 1)
                                {
                                    $memberFirstName = 'MyFirstName';
                                }
                                ?>
                                <input style="float: left; width:100%" id="txtFirstName"
                                       name="txtFirstName"
                                       <?= $isDisabled ?>
                                       required value="<?= $memberFirstName ?>" type="text" />
                            </div>
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                        </div>

                        <div class="row" style="margin-top: 4px">
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                            <div class="col-xs-12 col-sm-4 col-md-4">
                                <label style="float: left" for="txtLastName">Last Name:</label>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <input style="float: left; width:100%" id="txtLastName"
                                       name="txtLastName" <?= $isDisabled ?> required type="text" />
                            </div>
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                        </div>

                        <br/>

                        <div class="row" style="margin-top: 4px">
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                            <div class="col-xs-12 col-sm-4 col-md-4">
                                <label style="float: left" for="txtLogin">Login:</label>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <input style="float: left; width:100%" id="txtLogin"
                                       name="txtLogin" <?= $isDisabled ?> required minlength="8" type="text" />
                            </div>
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                        </div>

                        <div class="row" style="margin-top: 4px">
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                            <div class="col-xs-12 col-sm-4 col-md-4">
                                <label style="float: left" for="txtPassword">Password:</label>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <input style="float: left; width:100%" id="txtPassword"
                                       name="txtPassword" <?= $isDisabled ?> required minlength="10" type="text" />
                            </div>
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                        </div>

                        <br/>

                        <div class="row" style="margin-top: 4px">
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                            <div class="col-xs-12 col-sm-4 col-md-4">
                                <label style="float: left" for="txtHomePhone">Home Phone:</label>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <input style="float: left; width:100%" id="txtHomePhone"
                                       name="txtHomePhone" <?= $isDisabled ?> required type="text" />
                            </div>
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                        </div>

                        <div class="row" style="margin-top: 4px">
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                            <div class="col-xs-12 col-sm-4 col-md-4">
                                <label style="float: left" for="txtWorkPhone">Work Phone:</label>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <input style="float: left; width:100%" id="txtWorkPhone"
                                       name="txtWorkPhone" <?= $isDisabled ?> required type="text" />
                            </div>
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                        </div>

                        <div class="row" style="margin-top: 4px">
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                            <div class="col-xs-12 col-sm-4 col-md-4">
                                <label style="float: left" for="txtMobilePhone">Mobile Phone:</label>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <input style="float: left; width:100%" id="txtMobilePhone"
                                       name="txtMobilePhone" <?= $isDisabled ?> required type="text" />
                            </div>
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                        </div>

                        <br/>

                        <div class="row" style="margin-top: 4px">
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                            <div class="col-xs-12 col-sm-4 col-md-4">
                                <label style="float: left" for="txtAddress">Street Address:</label>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <input style="float: left; width:100%" id="txtAddress"
                                       name="txtAddress" <?= $isDisabled ?> required type="text" />
                            </div>
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                        </div>

                        <div class="row" style="margin-top: 4px">
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                            <div class="col-xs-12 col-sm-4 col-md-4">
                                <label style="float: left" for="txtSuburb">Suburb:</label>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <input style="float: left; width:100%" id="txtSuburb"
                                       name="txtSuburb" <?= $isDisabled ?> required type="text" />
                            </div>
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                        </div>

                        <div class="row" style="margin-top: 4px">
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                            <div class="col-xs-12 col-sm-4 col-md-4">
                                <label style="float: left" for="txtCity">City:</label>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <input style="float: left; width:100%" id="txtCity"
                                       name="txtCity" <?= $isDisabled ?> required type="text" />
                            </div>
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                        </div>

                        <br/>
                        <br/>

                        <div class="row" style="margin-top: 4px">
                            <div class="col-xs-0 col-sm-2 col-md-2">

                            </div>
                            <?php
                            if (isset($_SESSION[\Common\Security::$SessionAuthenticationKey]) && $_SESSION[\Common\Security::$SessionAuthenticationKey] == 1)
                            {
                                $submitValue = 'Save';

                                echo '<div class="col-xs-6 col-sm-3 col-md-3">' .
                                    '<input type="button" id="btnEditForm" onclick="make_form_editable();" value="Edit" />' .
									'<input type="reset" value="Reset" hidden id="resetForm" onclick="reset_form();" />' .
                                    '</div>' .
                                    '<div class="col-xs-12 col-sm-2 col-md-2">' .
									'<a href="../Pages/orders.php">Orders</a>' .
                                    '</div>' ;
                            }
                            else
                            {
                                $submitValue = 'Register';

                                echo '<div class="col-xs-6 col-sm-3 col-md-3">' .
                                    '<input type="reset" value="Reset" id="resetRegister" />' .
                                    '</div>' .
                                    '<div class="col-xs-0 col-sm-2 col-md-2">' .
                                    '</div>';
                            }
                            ?>
                            <div class="col-xs-6 col-sm-3 col-md-3">
                                <input style="float: right;" id="submit" name="submit"
                                    <?= $isDisabled ?> value="<?= $submitValue ?>" type="submit" />
                            </div>
                            <div class="col-xs-0 col-sm-2 col-md-2">

                            </div>
                        </div>
                        <br/>
                    </div>
                </div>
                <div id="divLeftSidebar" class="col-md-3">
                    <br/>
                    <?php print_r($_SESSION) ?>
                    <br/>
                    <?php print_r($_REQUEST) ?>
                </div>
            </div>

        </div>
        

    </form>

    <?php include_once("../Includes/footer.php"); ?>
</body>
</html>