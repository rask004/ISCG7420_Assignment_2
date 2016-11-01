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

$bad_login_message = "";

// in case of email error, notify visitor of successful registration but email failure.
if(isset($_SERVER["QUERY_STRING"][\Common\Constants::$QueryStringEmailErrorKey]))
{
	$ErrorMsg = "Customer was registered but could not send confirmation email. Please contact admin at ". $senderEmail ." immediately.";
}

// process postback - for testing, currently expect a particular user and pass (not real user).
if (isset($_POST['inputLogin']) && isset($_POST['inputPassword']))
{	
	$customerManager = new \BusinessLayer\CustomerManager;
	$adminManager = new \BusinessLayer\AdminManager;
	
	if ($customerManager->checkMatchingPasswordForCustomerLogin($_POST['inputLogin'], $_POST['inputPassword']))
	{
		// successful member login
		$customer = $customerManager->findCustomerByLogin($_POST['inputLogin']);
		
		$_SESSION[\Common\Security::$SessionAuthenticationKey] = 1;
   		$_SESSION[\Common\Security::$SessionUserLoginKey]  = $customer['login'];
    	$_SESSION[\Common\Security::$SessionUserIdKey] = $customer['id'];
		
		// prevent accidential misuse of member business layer objects.
		unset($customerManager);
		unset($customer);
	}
	elseif ($adminManager->checkMatchingPasswordForAdminLogin($_POST['inputLogin'], $_POST['inputPassword']))
	{
		// successful admin login
		$admin = $adminManager->findAdminByLogin($_POST['inputLogin']);
		
		$_SESSION[\Common\Security::$SessionAuthenticationKey] = 1;
   		$_SESSION[\Common\Security::$SessionUserLoginKey]  = $admin['login'];
    	$_SESSION[\Common\Security::$SessionUserIdKey] = $admin['id'];
		$_SESSION[\Common\Security::$SessionAdminCheckKey] = 1;
		
		// prevent accidential misuse of admin business layer objects.
		unset($adminManager);
		unset($admin);
	}
	else
	{
		$bad_login_message = "Login failed. Please check your you entered your login and password correctly.";	
	}
	
}

//  redirect already authenticated users - redirect to home or admin as appropriate.
if (isset($_SESSION[\Common\Security::$SessionAuthenticationKey]) && $_SESSION[\Common\Security::$SessionAuthenticationKey] == 1)
{
	if(isset($_SESSION[\Common\Security::$SessionAdminCheckKey]))
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
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.structure.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.theme.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/Common.css">
    <script type="text/javascript" src="../js/jquery.js"></script>
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
                                               name="inputPassword" required minlength="10" type="text" />
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
                    <div>
                    <p style="text-align:center">
                    	<?php echo $bad_login_message; ?>
                    </p>
                    </div>
                    <br/>
                    <div id="divErrorMessage">
	                    <p>
							<?php 
								// only show errors if a message is given.
								if (isset($ErrorMsg))
								{
									echo $ErrorMsg;	
								}
							?>
                        </p>
                    </div>
                </form>

            </div>
            <div id="divRightsidebar" class="col-md-3">
            </div>
        </div>
    </div>

    <?php include_once("../Includes/footer.php"); ?>
</body>
</html>

