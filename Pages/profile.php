<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 10:13 PM
 */
 
 ini_set('display_errors','1');

include_once('../Includes/Session.php');
include('../Includes/Common.php');
include_once("../Includes/CustomerManager.php");
	
$customerManager = new \BusinessLayer\CustomerManager;

$PostRegisterKey = \Common\Constants::$RegistrationSubmitKeyword;
$PostUpdateProfileKey = \Common\Constants::$ProfileUpdateKeyword;

// postback, when submitting new customer or updating profile.
if (isset($_POST["submit"]) && ($_POST["submit"] == $PostRegisterKey || $_POST["submit"] == $PostUpdateProfileKey))
{
	
	foreach( $_POST as $key => $value)
	{
		// prevent any sql injection by removing key symbols.
		if ($key != "txtPassword")
		{
			$_POST[$key] = str_replace(array("(", ")", ";", "%", "=", "<", ">"), "", $value);	
		}
	}
	
	// new customer validation, server side
	$isValid = true;
	
	// check if email or login is in use.
	if ($customerManager->findMatchingEmail($_POST["txtEmail"]) || $customerManager->findMatchingLogin($_POST["txtLogin"]))
	{
		$isValid = false;
		$ErrorMsg = "Supplied login, or email, is already in use. Try a different login name, or email.";
	}	
	// use regex for identifying valid entries, and if contact numbers are missing.
	if (empty($_POST["txtHomePhone"]) && empty($_POST["txtWorkPhone"]) && empty($_POST["txtMobilePhone"]) )
	{
		$isValid = false;
		$ErrorMsg = "At least one phone number must be given.";
	}
	$regex_output = array();
	preg_match(\Common\Constants::$ValidationCharsGenericNameRegex, $_POST["txtFirstName"], $regex_output);
	if (!($regex_output[0] === $_POST["txtFirstName"]) )
	{
		$isValid = false;
		$ErrorMsg = "Invalid first Name. Use letters, full stops, commas, or apostrophes only.";
	}
	preg_match(\Common\Constants::$ValidationCharsGenericNameRegex, $_POST["txtLastName"], $regex_output);
	if (!($regex_output[0] === $_POST["txtLastName"]) )
	{
		$isValid = false;	
		$ErrorMsg = "Invalid last Name. Use letters, full stops, commas, or apostrophes only.";
	}
	preg_match(\Common\Constants::$ValidationCharsLoginRegex, $_POST["txtLogin"], $regex_output);
	if (!($regex_output[0] === $_POST["txtLogin"]) )
	{
		$isValid = false;	
		$ErrorMsg = "Invalid login. Use letters, numbers and underscores only.";
	}
	preg_match(\Common\Constants::$ValidationLandlineRegex, $_POST["txtHomePhone"], $regex_output);
	if (!empty($_POST["txtHomePhone"]) && !($regex_output[0] === $_POST["txtHomePhone"]) )
	{
		$isValid = false;	
		$ErrorMsg = "Invalid home phone. Try a number in the form '0N-NNN-NNNN' or similar pattern. first digit must be a zero.";
	}
	preg_match(\Common\Constants::$ValidationLandlineRegex, $_POST["txtWorkPhone"], $regex_output);
	if (!empty($_POST["txtWorkPhone"]) && !($regex_output[0] === $_POST["txtWorkPhone"]) )
	{
		$isValid = false;	
		$ErrorMsg = "Invalid work phone. Try a number in the form '0N-NNN-NNNN' or similar pattern. first digit must be a zero.";
	}
	preg_match(\Common\Constants::$ValidationCellPhoneRegex, $_POST["txtMobilePhone"], $regex_output);
	if (!empty($_POST["txtMobilePhone"]) && !($regex_output[0] === $_POST["txtMobilePhone"]) )
	{
		$isValid = false;
		$ErrorMsg = "Invalid mobile phone. Try a number in the form '0NN-NNN-NNNN' or similar pattern. first digit must be a zero.";	
	}
	preg_match(\Common\Constants::$ValidationCharsGenericNameRegex, $_POST["txtSuburb"], $regex_output);
	if (!($regex_output[0] === $_POST["txtSuburb"]) )
	{
		$isValid = false;		
		$ErrorMsg = "Invalid last Name. Use letters, full stops, commas, or apostrophes only.";
	}
	preg_match(\Common\Constants::$ValidationCharsGenericNameRegex, $_POST["txtCity"], $regex_output);
	if (!($regex_output[0] === $_POST["txtCity"]) )
	{
		$isValid = false;		
		$ErrorMsg = "Invalid last Name. Use letters, full stops, commas, or apostrophes only.";
	}
	preg_match(\Common\Constants::$ValidationStreetAddressRegex, $_POST["txtAddress"], $regex_output);
	if (!($regex_output[0] === $_POST["txtAddress"]) )
	{
		$isValid = false;		
		$ErrorMsg = "Invalid address. Must be in form 'numbers[letter] name suffix' first number cannot be zero.";
	}
	if (!filter_var($_POST["txtEmail"], FILTER_VALIDATE_EMAIL))
	{
		$isValid = false;
		$ErrorMsg = "Invalid email. Must be in form 'name@site.domain', e.g. 'xli@yourunitec.ac.nz' or 'jnx@yourunitec.com'.";
	}
	
	// if valid, do registration / update profile and send email.
	if (false)
	{
		include_once("../Includes/BusinessLayer.php");
	
		$customerManager = new \BusinessLayer\CustomerManager;
	
		if ($_POST["submit"] == $PostRegisterKey)
		{
			if($customerManager->RegisterCustomer($_POST["txtFirstName"], $_POST["txtLastName"], $_POST["txtLogin"], $_POST["txtPassword"],
				$_POST["txtEmail"], $_POST["txtHomePhone"], $_POST["txtWorkPhone"], $_POST["txtMobilePhone"], $_POST["txtAddress"],
				$_POST["txtSuburb"], $_POST["txtCity"]))	
			{
				// request first available admin, obtain email
				
				$senderEmail = \Common\Constants::$EmailAdminDefault;
				$receiverEmail = $_POST["txtEmail"];
				$subject = "Quality Caps, Registered Customer";
				$body = "Dear Customer,\r\n\r\n\r\nWelcome to Quality Caps!\r\n\r\nYour Details are:\r\n\tLogin\t\t\t".$_POST["txtLogin"]."\r\n\tPassword\t\t".$_POST["txtPassword"]."\r\n\r\nYoursSincerely,\r\n\r\nThe QualityCapsTeam\r\n";
				$headers = 'From: '. $senderEmail. '\r\nReply-To: '. $senderEmail. '\r\nX-Mailer: PHP/'. phpversion();
				
				$mailSuccess = mail($receiverEmail, $subject, $body, $headers);
				
				$queryString = "";
				//redirect to login page. present message about mail failure if mail not sent.
				if ($mailSuccess)
				{
					$queryString .= "?". \Common\Constants::$QueryStringEmailErrorKey . "=1";
				}
				
				header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/login.php". $queryString);
				exit;
			}
			else
			{
				$ErrorMsg = "ERROR: could not register new customer. Please contact admin at ". $senderEmail ." immediately.";
			}
		}
		elseif ($_POST["submit"] == $PostUpdateProfileKey)
		{
			if($customerManager->UpdateProfile($_POST["txtFirstName"], $_POST["txtLastName"], $_POST["txtLogin"],
				$_POST["txtEmail"], $_POST["txtHomePhone"], $_POST["txtWorkPhone"], $_POST["txtMobilePhone"], $_POST["txtAddress"],
				$_POST["txtSuburb"], $_POST["txtCity"], $_SESSION[\Common\Security::$SessionUserIdKey] ))
			{
				// indicate primary update of profile worked.
				$successfulProfileUpdate = true;
				
				if(!$customerManager->UpdatePassword($_POST["txtPassword"], $_SESSION[\Common\Security::$SessionUserIdKey]))
				{
					$ErrorMsg = "ERROR: Updated profile but could not change password. Please contact admin at ". $senderEmail ." immediately.";
				}
				else
				{
					// request first available admin, obtain email.
				
					$senderEmail = \Common\Constants::$EmailAdminDefault;
					$receiverEmail = $_POST["txtEmail"];
					$subject = "Quality Caps, Registered Customer";
					$body = "Dear Customer,\r\n\r\n\r\nWelcome to Quality Caps!\r\n\r\nYour Details are:\r\n\tLogin\t\t\t".$_POST["txtLogin"]."\r\n\tPassword\t\t".$_POST["txtPassword"]."\r\n\r\nYoursSincerely,\r\n\r\nThe QualityCapsTeam\r\n";
					$headers = 'From: '. $senderEmail. '\r\nReply-To: '. $senderEmail. '\r\nX-Mailer: PHP/'. phpversion();
					
					if (!mail($receiverEmail, $subject, $body, $headers))
					{
						$ErrorMsg = "Customer was registered but could not send confirmation email. Please contact admin at ". $senderEmail ." immediately.";
					}
				}
			}
			else
			{
				$ErrorMsg = "ERROR: could not update profile. Please contact admin at ". $senderEmail ." immediately.";
			}
		}
		
	}
	
	
}

if (isset($_SESSION[\Common\Security::$SessionAuthenticationKey]) && isset($_SESSION[\Common\Security::$SessionAdminCheckKey]))
{
    header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/AdminFiles.php");
    exit;
}


// setup page for logged in user (Profile) or visitor (Registration)
if (isset($_SESSION[\Common\Security::$SessionAuthenticationKey]) && $_SESSION[\Common\Security::$SessionAuthenticationKey] == 1)
{
    $isDisabled = 'disabled';
	
	$customer = $customerManager->findCustomer($_SESSION[\Common\Security::$SessionUserIdKey]);
	if (empty($customer))
	{
		$ErrorMsg = "ERROR: could not retrieve logged in customer information. Try logging out and back in. If problem persists, contact admin at " .
			\Common\Constants::$EmailAdminDefault . " Immediately";	
	}
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
				$("#btnChangeProfilePassword").prop( 'disabled', false);
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
				$("#btnChangeProfilePassword").prop( 'disabled', true);
				$("#btnChangeProfilePassword").val("Change Password" );
				$("#txtPassword").prop( 'disabled', true);
				$("#txtPassword").val('');
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
		
		function profile_password_toggle() 
		{
				if($("#btnChangeProfilePassword").val() == "Change Password")
				{
					$("#btnChangeProfilePassword").val("Reset Password" );
					$("#txtPassword").prop( 'disabled', false);
				}
				else
				{
					$("#btnChangeProfilePassword").val("Change Password" );
					$("#txtPassword").prop( 'disabled', true);
					$("#txtPassword").val('');
				}
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
					<?php print_r($customer) ?>
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
                                
                                <input style="float: left; width:100%" id="txtFirstName"
                                       name="txtFirstName" value="<?php if(isset($customer)) { echo $customer["firstName"]; } ?>"
                                       <?= $isDisabled ?>
                                       required maxlength="32" type="text" />
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
                                       name="txtLastName" value="<?php if (isset($customer)) { echo $customer["lastName"]; } ?>"
                                       <?= $isDisabled ?> required maxlength="32" type="text" />
                            </div>
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                        </div>
                        
                        <div class="row" style="margin-top: 4px">
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                            <div class="col-xs-12 col-sm-4 col-md-4">
                                <label style="float: left" for="txtEmail">Email:</label>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <input style="float: left; width:100%" id="txtEmail"
                                       name="txtEmail" value="<?php if (isset($customer)) { echo $customer["emailAddress"]; } ?>" 
									   <?= $isDisabled ?> required minlength="5" maxlength="100" type="text" />
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
                                       name="txtLogin" value="<?php if (isset($customer)) { echo $customer["login"]; } ?>" 
									   <?= $isDisabled ?> required minlength="8" maxlength="32" type="text" />
                            </div>
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                        </div>

                        <div class="row" style="margin-top: 4px">
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                            <?php
                            if (isset($_SESSION[\Common\Security::$SessionAuthenticationKey]) && $_SESSION[\Common\Security::$SessionAuthenticationKey] == 1)
                            {
								echo '<div class="col-xs-12 col-sm-4 col-md-4">' .
									 '<input type="button" id="btnChangeProfilePassword" '.
									 ' disabled onclick="profile_password_toggle();" style="float: left; width:80%" value="Change Password" />' .
									 '</div>' .
									 '<div class="col-xs-12 col-sm-6 col-md-4">'.
										 '<input style="float: left; width:100%" id="txtPassword"' .
											    ' name="txtPassword"  value="" ' .
											    ' disabled required minlength="10" type="text" />' .
									 '</div>';
							}
							else
							{
								echo '<div class="col-xs-12 col-sm-4 col-md-4">' .
									 '<label style="float: left" for="txtPassword">Password:</label>' .
									 '</div>' .
									 '<div class="col-xs-12 col-sm-6 col-md-4">'.
										 '<input style="float: left; width:100%" id="txtPassword"' .
											    ' name="txtPassword"  value="" ' .
											    $isDisabled . ' required minlength="10" type="text" />' .
									 '</div>';
							}
							?>
                            
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
                                       name="txtHomePhone"  value="<?php if (isset($customer)) { echo $customer["homeNumber"]; } ?>"
									   <?= $isDisabled ?> minlength="8" maxlength="10"  type="text" />
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
                                       name="txtWorkPhone"  value="<?php if (isset($customer)) { echo $customer["workNumber"]; } ?>"
									   <?= $isDisabled ?> minlength="8" maxlength="10" type="text" />
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
                                       name="txtMobilePhone"  value="<?php if (isset($customer)) { echo $customer["mobileNumber"]; } ?>"
									   <?= $isDisabled ?> minlength="9" maxlength="11" type="text" />
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
                                       name="txtAddress"  value="<?php if (isset($customer)) { echo $customer["streetAddress"]; } ?>"
									   <?= $isDisabled ?> required  minlength="3" maxlength="48" type="text" />
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
                                       name="txtSuburb"  value="<?php if (isset($customer)) { echo $customer["suburb"]; } ?>"
									   <?= $isDisabled ?> required maxlength="24" type="text" />
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
                                       name="txtCity"  value="<?php if (isset($customer)) { echo $customer["city"]; } ?>"
									   <?= $isDisabled ?> required maxlength="24" type="text" />
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
                </div>
                <div id="divLeftSidebar" class="col-md-3">
                    <br/>
                    <?php print_r($_POST) ?>
                    <br/>
					<?php 
						if (isset($isValid) && !$isValid)
						{
							echo '<p>$IsValid == FALSE</p>';
						}
						else
						{
							echo '<p>$IsValid == TRUE</p>';
						}
					?>	
                </div>
            </div>

        </div>
        

    </form>

    <?php include_once("../Includes/footer.php"); ?>
    <?php 
		// make div for error message more visible
		if (isset($ErrorMsg))
		{
			echo '<script type="text/javascript">'.
				'$("#divErrorMessage").prop'."('border', 'solid black 2px');".
				'$("#divErrorMessage").prop'."('background-color', 'red');".
				'</script>';
		}
		if (isset($successfulProfileUpdate))
		{
			echo '<script type="text/javascript">'.
				'reset_form();'.
				'</script>';
		}
	?>
</body>
</html>