<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 10:09 PM
 */

include_once('../Includes/Session.php');
include_once('../Includes/Common.php');
include_once('../Includes/CustomerManager.php');
include_once('../Includes/AdminManager.php');

use BusinessLayer\CustomerManager;
use BusinessLayer\AdminManager;

$customerId = "VISITOR";
if(isset($_SESSION[\Common\SecurityConstraints::$SessionUserIdKey]))
{
    $customerId = $_SESSION[\Common\SecurityConstraints::$SessionUserIdKey];
}

\Common\Logging::Log('Executing Page. sessionId=' . session_id() . '; customer='
    . $customerId . "\r\n");

$msg = "";
$senderEmail = \Common\Constants::$EmailAdminDefault;

// in case of email error, notify visitor of successful registration but email failure.
if(isset($_GET[\Common\Constants::$QueryStringEmailErrorKey]))
{
	$msg = "Customer was registered but could not send confirmation email. Please contact admin at ". $senderEmail ." immediately.";
}
else if (isset($_GET[\Common\Constants::$QueryStringEmailSuccessKey]))
{
	$msg = "You have been successfully registered and may now log in.";
}

if (isset($_POST['inputLogin']) && isset($_POST['inputPassword']))
{	
	$CustomerManager = new CustomerManager();
	
	if ($CustomerManager->CheckMatchingPasswordForCustomerLogin($_POST['inputLogin'], $_POST['inputPassword']))
	{
        $Customer = $CustomerManager->FindCustomerByLogin($_POST['inputLogin']);

		// check if customer is disabled.if so, post warning
		if ($Customer['isDisabled'] == 1)
		{
			$msg = "This account is disabled. If you believe this is in error, contact the Admin at " . $senderEmail ." immediately.";
		}
		else
		{

			// successful member login
			$_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey] = 1;
   			$_SESSION[\Common\SecurityConstraints::$SessionUserLoginKey]  = $Customer['login'];
    			$_SESSION[\Common\SecurityConstraints::$SessionUserIdKey] = $Customer['id'];
		}
		
		// prevent accidential misuse of member business layer objects.
		unset($Customer);
	}
	else
    {
        $AdminManager = new AdminManager;
        if ($AdminManager->CheckMatchingPasswordForAdminLogin($_POST['inputLogin'], $_POST['inputPassword']))
        {
            // successful admin login
            $Admin = $AdminManager->FindAdminByLogin($_POST['inputLogin']);

            $_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey] = 1;
            $_SESSION[\Common\SecurityConstraints::$SessionUserLoginKey]  = $Admin['login'];
            $_SESSION[\Common\SecurityConstraints::$SessionUserIdKey] = $Admin['id'];
            $_SESSION[\Common\SecurityConstraints::$SessionAdminCheckKey] = 1;

            // prevent accidential misuse of admin business layer objects.
            unset($AdminManager);
            unset($Admin);
        }
        else
        {
            $msg = "Login failed. Please check your you entered your login and password correctly.";
        }
    }
	
}

//  redirect already authenticated users - redirect to home or admin as appropriate.
if (isset($_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey]) && $_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey] == 1)
{
	if(isset($_SESSION[\Common\SecurityConstraints::$SessionAdminCheckKey]))
	{
		header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/AdminFiles.php");
    	exit;
	}
	else
	{
		header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/home.php");
    	exit;
	}
    
}

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quality Caps - Login</title>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/Common.css">
    <script type="text/javascript" src="../js/jquery.min.js"></script>
</head>

<body>
    <?php 
		// assume visiting user is visitor - as only visitor needs to login.
		include_once("../Includes/navbar.visitor.php"); 
	?>

    <div class="container-fluid PageContainer">

        <div class="row">
            <div id="divLeftSidebar" class="col-md-3">
            </div>
            <div id="divCentreSpace" class="col-md-6">

                <form method="post" enctype="multipart/form-data">
                    <div class="container-fluid panel panel-default PageSection">
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
                                        <input class="form-control" style="float: right; width:100%" id="inputLogin"
                                               name="inputLogin" required minlength="6" type="text" />
                                    </div>
                                </div>
                                <div class="row" style="margin-top: 4px">
                                    <div class="col-xs-12 col-sm-4 col-md-5">
                                        <label style="float: left" for="inputPassword">Password:</label>
                                    </div>
                                    <div class="col-xs-12 col-sm-8 col-md-7">
                                        <input class="form-control" style="float: right; width:100%" id="inputPassword"
                                               name="inputPassword" required minlength="10" type="password" />
                                    </div>
                                </div>

                                <br/>
                                <div class="row" style="margin-top: 4px">
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <input class="btn btn-primary" style="width:100%" type="reset" value="Clear" />
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">

                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <input class="btn btn-primary" style="width:100%" name="submit" type="submit" value="Submit" />
                                    </div>
                                </div>

                                <br/>

                            </div>
                            <div class="col-xs-0 col-sm-2 col-md-2">
                            </div>
                        </div>
                    </div>
                    
                    <br/>
                    <br/>
                </form>
                
                <!-- auto-sliding message to show when an order is successfully placed. -->
                <div hidden id="divLoginMsg" class="alert alert-danger" role="alert">
                  <span id="msgContent"><?php echo $msg ?></span>
                </div>

            </div>
            <div id="divRightsidebar" class="col-md-3">
            </div>
        </div>
    </div>
    
    <?php
		// if there is notification to show, show it.
		if(isset($msg) && !empty($msg) )
		{
			// registration notifications override login warnings.
			// besides, they should be mutually exclusive.
			if (isset($_GET) && !empty($_GET))
			{
				if(isset($_GET[\Common\Constants::$QueryStringEmailSuccessKey]))
				{
					echo '<script type="text/javascript">$("#divLoginMsg").prop("class","alert alert-success");</script>';
					
				}
				else if (isset($_GET[\Common\Constants::$QueryStringEmailErrorKey]))
				{
					echo '<script type="text/javascript">$("#divLoginMsg").prop("class","alert alert-warning");</script>';
				}
			}
			
			// make notification div appear.
			echo '<script type="text/javascript">'.
				 '$("#divLoginMsg").prop("hidden", false);'.
				 'window.setTimeout(function() {'.
					'$(".alert").fadeTo(500, 0).slideUp(500, function()	{'.
						'$(this).remove(); '.
					'});'.
				 '}, 4000);'.
				 '</script>';
			
		}
	?>

    <?php include_once("../Includes/footer.php"); ?>
</body>
</html>

