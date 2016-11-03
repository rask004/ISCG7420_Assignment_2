<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 10:13 PM
 *
 *	NOTE: This page is both the register new customer page, AND the update member profile page.
 */
 
 ini_set("display_errors","1");
 
include_once('../Includes/Session.php');
include_once('../Includes/Common.php');
include_once("../Includes/CustomerManager.php");

// for adding and updating customers.
$CustomerManager = new \BusinessLayer\CustomerManager;

$postRegisterKey = \Common\Constants::$RegistrationSubmitKeyword;
$postUpdateProfileKey = \Common\Constants::$ProfileUpdateKeyword;

// postback, when submitting new customer or updating profile.
if (isset($_POST["submit"]) && @strcmp($_POST["submit"], $postRegisterKey) === 0 || @strcmp($_POST["submit"], $postUpdateProfileKey) === 0)
{
	
	foreach( $_POST as $key => $value)
	{
		// prevent any sql injection by removing key SQL symbols.
		// Note that password will be HMAC-parsed as strict string text, so can ignore password.
		if ($key != "txtPassword")
		{
			$_POST[$key] = str_replace(array("(", ")", ";", "%", "=", "<", ">"), "", $value);	
		}
	}
	
	// for customer validation, server side
	$isValid = true;
	
	// check if login is in use.
	if ($CustomerManager->FindMatchingLogin($_POST["txtLogin"]))
	{
		
		// if register, always fail on a match
		if ($_POST["submit"] == $postRegisterKey)
		{
			$isValid = false;
			$errorMsg = "Supplied login is already in use. Try a different login name.";
		}
		// if profile update, and login unchanged, then ignore the match
		else
		{
			$customer = $CustomerManager->FindCustomer($_SESSION[\Common\SecurityConstraints::$SessionUserIdKey]);
			$oldLogin = $customer["login"];
			
			if ($oldLogin != $_POST["txtLogin"])
			{
				$isValid = false;
				$errorMsg = "Supplied login is already in use. Try a different login name.";
			}
		}
	}
	// ignore if updating profile
	// profile updates do not allow email changes.
	if ($_POST["submit"] == $postRegisterKey && $CustomerManager->FindMatchingEmail($_POST["txtEmail"]))
	{		
		$isValid = false;
		$errorMsg = "Supplied email, is already in use. Try a different email.";
	}	
	// use regex for identifying valid entries, and if contact numbers are missing.
	if ($isValid && empty($_POST["txtHomePhone"]) && empty($_POST["txtWorkPhone"]) && empty($_POST["txtMobilePhone"]) )
	{
		$isValid = false;
		$errorMsg = "At least one phone number must be given.";
	}
	$regex_output = array();
	preg_match(\Common\Constants::$ValidationCharsGenericNameRegex, $_POST["txtFirstName"], $regex_output);
	if ($isValid && (empty($regex_output) || !($regex_output[0] === $_POST["txtFirstName"])) )
	{
		$isValid = false;
		$errorMsg = "Invalid first Name. Use letters, full stops, commas, or apostrophes only.";
	}
	preg_match(\Common\Constants::$ValidationCharsGenericNameRegex, $_POST["txtLastName"], $regex_output);
	if ($isValid && (empty($regex_output) || !($regex_output[0] === $_POST["txtLastName"])) )
	{
		$isValid = false;	
		$errorMsg = "Invalid last Name. Use letters, spaces, full stops, commas, or apostrophes only.";
	}
	preg_match(\Common\Constants::$ValidationCharsLoginRegex, $_POST["txtLogin"], $regex_output);
	if ($isValid && (empty($regex_output) || !($regex_output[0] === $_POST["txtLogin"])) )
	{
		$isValid = false;	
		$errorMsg = "Invalid login. Use letters, numbers and underscores only.";
	}
	preg_match(\Common\Constants::$ValidationLandlineRegex, $_POST["txtHomePhone"], $regex_output);
	if ($isValid && !empty($_POST["txtHomePhone"]) && (empty($regex_output) || !($regex_output[0] === $_POST["txtHomePhone"])) )
	{
		$isValid = false;	
		$errorMsg = "Invalid home phone. Try a number in the form '0N-NNN-NNNN' or similar pattern. first digit must be a zero.";
	}
	preg_match(\Common\Constants::$ValidationLandlineRegex, $_POST["txtWorkPhone"], $regex_output);
	if ($isValid && !empty($_POST["txtWorkPhone"]) && (empty($regex_output) || !($regex_output[0] === $_POST["txtWorkPhone"])) )
	{
		$isValid = false;	
		$errorMsg = "Invalid work phone. Try a number in the form '0N-NNN-NNNN' or similar pattern. first digit must be a zero.";
	}
	preg_match(\Common\Constants::$ValidationCellPhoneRegex, $_POST["txtMobilePhone"], $regex_output);
	if ($isValid && !empty($_POST["txtMobilePhone"]) && (empty($regex_output) || !($regex_output[0] === $_POST["txtMobilePhone"])) )
	{
		$isValid = false;
		$errorMsg = "Invalid mobile phone. Try a number in the form '0NN-NNN-NNNN' or similar pattern. first digit must be a zero.";	
	}
	preg_match(\Common\Constants::$ValidationCharsGenericNameRegex, $_POST["txtSuburb"], $regex_output);
	if ($isValid && (empty($regex_output) || !($regex_output[0] === $_POST["txtSuburb"]) ))
	{
		$isValid = false;		
		$errorMsg = "Invalid suburb. Use letters, full stops, spaces, commas, or apostrophes only.";
	}
	preg_match(\Common\Constants::$ValidationCharsGenericNameRegex, $_POST["txtCity"], $regex_output);
	if ($isValid && (empty($regex_output) || !($regex_output[0] === $_POST["txtCity"]) ))
	{
		$isValid = false;		
		$errorMsg = "Invalid city. Use letters, full stops, spaces, commas, or apostrophes only.";
	}
	preg_match(\Common\Constants::$ValidationStreetAddressRegex, $_POST["txtAddress"], $regex_output);
	if ($isValid && (empty($regex_output) || !($regex_output[0] === $_POST["txtAddress"]) ))
	{
		$isValid = false;		
		$errorMsg = "Invalid address. Must be in form '[flat number/]numbers[letter] name suffix'. The first number cannot be zero.";
	}
	// filter_var for email is simpler than regex.
	// only check email validity if registering - it will be unchanged for profile update
	if (isset($_POST["txtEmail"]) && $isValid && !filter_var($_POST["txtEmail"], FILTER_VALIDATE_EMAIL))
	{
		$isValid = false;
		$errorMsg = "Invalid email. Must be in form 'name@site.domain', e.g. 'xli@yourunitec.ac.nz' or 'jnx@yourunitec.com'.";
	}
	
	// if valid, do registration / update profile and send email.
	if ($isValid)
	{	
		if (strcmp($_POST["submit"], $postRegisterKey) == 0 )
		{
			if($CustomerManager->RegisterCustomer($_POST["txtFirstName"], $_POST["txtLastName"], $_POST["txtLogin"], $_POST["txtPassword"],
				$_POST["txtEmail"], $_POST["txtHomePhone"], $_POST["txtWorkPhone"], $_POST["txtMobilePhone"], $_POST["txtAddress"],
				$_POST["txtSuburb"], $_POST["txtCity"]))	
			{
				// simpler to use a default admin email				
				$senderEmail = \Common\Constants::$EmailAdminDefault;
				$receiverEmail = $_POST["txtEmail"];
				$subject = "Quality Caps, Registered Customer";
				// formats the text correctly
				$body = wordwrap("Dear Customer,\r\n\r\n\r\nWelcome to Quality Caps!\r\n\r\nYour Details are:\r\n\r\n\tLogin         ".$_POST["txtLogin"].
						"\r\n\tPassword      ".$_POST["txtPassword"]."\r\n\r\nYours Sincerely,\r\n\r\nThe Quality Caps Team\r\n", 70, "\r\n");
				
				// mail method provided in tutorial slides did not seem to work.
				// this method borrowed from stackoverflow. appears to work.
				$headers = "From: ". $senderEmail. "\r\n";
				$headers .= "Reply-To: ". $senderEmail. "\r\n";
				// mail is sent as plain literal text.
				$headers .= "Content-Type: text/plain; charset=us-ascii \n";
				$headers .= "MIME-Version: 1.0 \r\n";
				
				$queryString = "";
				//redirect to login page. present message about mail failure if mail not sent.
				if (!mail($receiverEmail, $subject, $body, $headers))
				{
					$queryString .= "?". \Common\Constants::$QueryStringEmailErrorKey . "=1";
				}
				else
				{
					$queryString .= "?". \Common\Constants::$QueryStringEmailSuccessKey . "=1";
				}
				
				header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/login.php". $queryString);
				exit;
			}
			else
			{
				$errorMsg = "ERROR: could not register new customer. Please contact the admin at ". $senderEmail ." immediately.";
			}
		}
		elseif (strcmp($_POST["submit"], $postUpdateProfileKey) == 0)
		{
			if($CustomerManager->UpdateProfile($_POST["txtFirstName"], $_POST["txtLastName"], $_POST["txtLogin"],
				$_POST["txtEmail"], $_POST["txtHomePhone"], $_POST["txtWorkPhone"], $_POST["txtMobilePhone"], $_POST["txtAddress"],
				$_POST["txtSuburb"], $_POST["txtCity"], $_SESSION[\Common\SecurityConstraints::$SessionUserIdKey] ))
			{
				// indicate primary update of profile worked.
				$successfulPrimaryProfileUpdate = true;
				
				// check if password update is requested too
				if (!empty($_POST["txtPassword"]))
				{
					// try password update
					if ($CustomerManager->UpdatePassword($_POST["txtPassword"], $_SESSION[\Common\SecurityConstraints::$SessionUserIdKey]))
					{
						// success - indicate this.
						$successfulPasswordUpdate = true;
					}
					else
					{
						
						// failure - post warning message
						$errorMsg = "ERROR: Updated profile but could not change password. Please contact admin at ". $senderEmail ." immediately.";
					}
				}
				else
				{
					// do nothing - not updating the password
				}
				
				if (isset($successfulPrimaryProfileUpdate))
				{
					// must notify customer that profile has changed.
					$senderEmail = \Common\Constants::$EmailAdminDefault;
					$receiverEmail = $_POST["txtEmail"];
					$subject = "Quality Caps, Profile Update";
					
					if (isset($successfulPasswordUpdate))
					{
						// if password changed
						$body = wordwrap("Dear Customer,\r\n\r\n\r\nYour profile has been updated.\r\n\r\nYour login details are:".
								"\r\n\r\n\tLogin         ". $_POST["txtLogin"]. "\r\n\tPassword      ".$_POST["txtPassword"].
								"\r\n\r\nYours Sincerely,\r\n\r\nThe Quality Caps Team\r\n", 70, "\r\n");
					}
					else
					{
						// without password change.
						$body = wordwrap("Dear Customer,\r\n\r\n\r\nYour profile has been updated.\r\n\r\nYour login details have not changed.".
								"\r\n\r\nIf you are not responsible for this, please contact administration immediately.".
								"\r\n\r\nYours Sincerely,\r\n\r\nThe Quality Caps Team\r\n", 70, "\r\n");
					}
					
					$headers = "From: ". $senderEmail. "\r\n";
					$headers .= "Reply-To: ". $senderEmail. "\r\n";
					$headers .= "Content-Type: text/html; charset=TIS-620 \n";
					$headers .= "MIME-Version: 1.0 \r\n";
					
					if (!mail($receiverEmail, $subject, $body, $headers))
					{
						$errorMsg = "Profile was updated but could not send confirmation email.".
									" Please contact admin at ". $senderEmail .".";
					}
				}
			}
			else
			{
				$errorMsg = "ERROR: could not update profile. Please notify the admin at ". $senderEmail ." immediately.";
			}
		}
	}	
}

// redirect admin users to the admin page.
if (isset($_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey]) 
	&& isset($_SESSION[\Common\SecurityConstraints::$SessionAdminCheckKey]))
{
    header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/AdminFiles.php");
    exit;
}


// setup page for logged in user (Profile) or visitor (Registration)
if (isset($_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey]) 
	&& $_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey] == 1)
{
	// if this is a logged in user, then initially all fields are disabled, until user indicates they want to edit profile details.
    $isDisabled = 'readonly';
	
	$customer = $CustomerManager->FindCustomer($_SESSION[\Common\SecurityConstraints::$SessionUserIdKey]);
	if (empty($customer))
	{
		$errorMsg = "ERROR: could not retrieve logged in customer information. Try logging out and back in. If problem persists, contact admin at " .
			\Common\Constants::$EmailAdminDefault . " Immediately";	
	}
}
else
{
	// otherwise assume this is a registering visitor - so permit full data entry as per normal registering.
    $isDisabled = '';
}

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quality Caps -
        <?php
		// set correct browser tab title.
        if (isset($_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey]) 
			&& $_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey] == 1)
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
	
		// for customer update form.
		// pressing edit allows changes to the form and shows reset button.
		// pressing reset undos current and disables further changes, and shows edit button.
		function change_form() 
		{
			if($("#btnEditForm").val() == "Edit")
			{
				$("#txtFirstName").prop( 'readonly', false);
				$("#txtLastName").prop( 'readonly', false);
				$("#txtLogin").prop( 'readonly', false);
				$("#btnChangeProfilePassword").prop( 'disabled', false);
				$("#txtHomePhone").prop( 'readonly', false);
				$("#txtWorkPhone").prop( 'readonly', false);
				$("#txtMobilePhone").prop( 'readonly', false);
				$("#txtAddress").prop( 'readonly', false);
				$("#txtSuburb").prop( 'readonly', false);
				$("#txtCity").prop( 'readonly', false);
				$("#submit").prop( 'readonly', false);
				$("#btnEditForm").val("Reset");
			}
			else
			{
				$("#txtFirstName").prop( 'readonly', true);
				$("#txtLastName").prop( 'readonly', true);
				$("#txtLogin").prop( 'readonly', true);
				$("#btnChangeProfilePassword").prop( 'disabled', true);
				$("#btnChangeProfilePassword").val("Change Password" );
				$("#txtPassword").prop( 'readonly', true);
				$("#txtPassword").val('');
				$("#txtHomePhone").prop( 'readonly', true);
				$("#txtWorkPhone").prop( 'readonly', true);
				$("#txtMobilePhone").prop( 'readonly', true);
				$("#txtAddress").prop( 'readonly', true);
				$("#txtSuburb").prop( 'readonly', true);
				$("#txtCity").prop( 'readonly', true);
				$("#submit").prop( 'readonly', true);
				$("#btnEditForm").val("Edit");
				$("#resetProfile").click();
			}
		}
		
		// toggles allowing password changes. Only for updating a member's profile.
		// toggle on to allow entering a new password, for updating.
		// toggle off to clear any new password entry, disable password editing and retain old password.
		function profile_password_toggle() 
		{
				if($("#btnChangeProfilePassword").val() == "Change Password")
				{
					$("#btnChangeProfilePassword").val("Reset Password" );
					$("#txtPassword").prop( 'readonly', false);
				}
				else
				{
					$("#btnChangeProfilePassword").val("Change Password" );
					$("#txtPassword").prop( 'readonly', true);
					$("#txtPassword").val('');
				}
		}
			
    </script>
</head>

<body>
    <?php
	// load correct navbar for visitor or logged in user.
    if (isset($_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey]) 
		&& $_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey] == 1)
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
                    <div class="container-fluid panel panel-default  PageSection">
                        <br/>

                        <div class="row" style="margin: auto 20px">
                            <div class="col-xs-0 col-sm-4 col-md-3">
                            </div>
                            <div class="col-xs-12 col-sm-4 col-md-6 DecoHeader">
                                <H3>
                                    <?php
										// show correct title
                                        if (isset($_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey]) 
											&& $_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey] == 1)
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
                        
                        <!-- 
                        	for each data entry field, if registering as new customer,  it should be initially blank.
                            	also password should show password label and blank field.
                        	otherwise fill each entry field with the relevant member data, and show
                            	password editing button.
                                
                            if visitor submitted for registering, but submission was rejected, retain all entered data
                            	so visitor can edit errors without re-entering all details.
                        -->

                        <div class="row" style="margin-top: 4px">
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                            <div class="col-xs-12 col-sm-4 col-md-4">
                                <label class="label label-default" style="margin-top:4px; float: left; 
                                	font-size:0.9em" for="txtFirstName">First Name:</label>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                
                                <input class="form-control" style="float: left; width:100%" id="txtFirstName"
                                        name="txtFirstName" 
									    <?php 
											// show customer details for logged in user
											if(isset($customer)) 
											{ 
												echo 'value="' . $customer["firstName"] . '"';
											} 
											// otherwise show either nothing, or if a previous submissin failed, the value that was submitted.
											elseif (isset($_POST["submit"]) && ($_POST["submit"] == $postRegisterKey))
											{
												echo 'value="' .$_POST["txtFirstName"]. '"';
											}
										?>
                                        
                                        <?php 
											echo $isDisabled;
										?>
                                        required maxlength="32" type="text" />
                            </div>
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                        </div>

                        <div class="row" style="margin-top: 4px">
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                            <div class="col-xs-12 col-sm-4 col-md-4">
                                <label class="label label-default" style="margin-top:4px; float: left; 
                                	font-size:0.9em" for="txtLastName">Last Name:</label>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <input class="form-control" style="float: left; width:100%" id="txtLastName"
                                       name="txtLastName"
									   <?php 
											if(isset($customer)) 
											{ 
												echo 'value="' . $customer["lastName"] . '"';
											} 
											elseif (isset($_POST["submit"]) && ($_POST["submit"] == $postRegisterKey))
											{
												echo 'value="' . $_POST["txtLastName"] . '"';
											}
										?>
                                       
									   <?php 
											echo $isDisabled;
										?>
                                        
                                         required maxlength="32" type="text" />
                            </div>
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                        </div>
                        
                        <div class="row" style="margin-top: 4px">
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                            <div class="col-xs-12 col-sm-4 col-md-4">
                                <label class="label label-default" style="margin-top:4px; float: left; 
                                	font-size:0.9em" for="txtEmail">Email:</label>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <input class="form-control" style="float: left; width:100%" id="txtEmail"
                                       name="txtEmail" 
									   <?php 
									   		if(isset($customer)) 
											{ 
												echo 'value="' . $customer["emailAddress"] . '"';
											} 
											elseif (isset($_POST["submit"]) && ($_POST["submit"] == $postRegisterKey))
											{
												echo 'value="' . $_POST["txtEmail"] . '"';
											}
									   ?>
                                       readonly required minlength="5" maxlength="100" type="text" />
                            </div>
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                        </div>

                        <br/>

                        <div class="row" style="margin-top: 4px">
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                            <div class="col-xs-12 col-sm-4 col-md-4">
                                <label class="label label-default" style="margin-top:4px; float: left; 
                                	font-size:0.9em" for="txtLogin">Login:</label>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <input class="form-control" style="float: left; width:100%" id="txtLogin"
                                       name="txtLogin" 
									   <?php 
									   		if (isset($customer)) 
											{ 
												echo 'value="' . $customer["login"] . '"';
											} 											 
											elseif (isset($_POST["submit"]) && ($_POST["submit"] == $postRegisterKey))
											{
												echo 'value="' . $_POST["txtLogin"] . '"';
											}
										?>
                                       
									   <?php 
											echo $isDisabled;
										?>
                                        
                                         required minlength="8" maxlength="32" type="text" />
                            </div>
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                        </div>

                        <div class="row" style="margin-top: 4px">
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                            <?php
							// show either password toggle button and password update field, for logged in user,
							// or password label and new password field for visitor.
                            if (isset($_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey]) 
								&& $_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey] == 1)
                            {
								echo '<div class="col-xs-12 col-sm-4 col-md-4">' .
									 '<input type="button" class="btn btn-warning" id="btnChangeProfilePassword" '.
									 ' disabled onclick="profile_password_toggle();" style="float: left; width:80%" value="Change Password" />' .
									 '</div>' .
									 '<div class="col-xs-12 col-sm-6 col-md-4">'.
										 '<input class="form-control" style="float: left; width:100%" id="txtPassword"' .
											    ' name="txtPassword"  value="" ' .
											    ' readonly required minlength="10" type="password" />' .
									 '</div>';
							}
							else
							{
								$password = "";
								// in case new customer submission is rejected, retain the given password for visitor to edit.
								if (isset($_POST["submit"]) && ($_POST["submit"] == $postRegisterKey))
								{
									$password .= $_POST["txtPassword"];
								}
								echo '<div class="col-xs-12 col-sm-4 col-md-4">' .
									 '<label class="label label-default" style="margin-top:4px; float: left; font-size:0.9em" ' .
									 'for="txtPassword">Password:</label>' .
									 '</div>' .
									 '<div class="col-xs-12 col-sm-6 col-md-4">' .
										 '<input class="form-control" style="float: left; width:100%" id="txtPassword"' .
											    ' name="txtPassword"  value="' . $password . '" ' .
											    ' required minlength="10" type="password" />' .
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
                                <label class="label label-default" style="margin-top:4px; float: left; 
                                	font-size:0.9em" for="txtHomePhone">Home Phone:</label>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <input class="form-control" style="float: left; width:100%" id="txtHomePhone"
                                       name="txtHomePhone"
									   <?php 
									   		if (isset($customer)) 
											{ 
												echo 'value="' . $customer["homeNumber"] . '"';
											} 											 
											elseif (isset($_POST["submit"]) && ($_POST["submit"] == $postRegisterKey))
											{
												echo 'value="' . $_POST["txtHomePhone"] . '"';
											}
										?>
                                       
									   <?php 
											echo $isDisabled;
										?>
                                        
                                         minlength="8" maxlength="13"  type="text" />
                            </div>
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                        </div>

                        <div class="row" style="margin-top: 4px">
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                            <div class="col-xs-12 col-sm-4 col-md-4">
                                <label class="label label-default" style="margin-top:4px; float: left; 
                                	font-size:0.9em" for="txtWorkPhone">Work Phone:</label>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <input class="form-control" style="float: left; width:100%" id="txtWorkPhone"
                                       name="txtWorkPhone" 
									   <?php 
									   		if (isset($customer)) 
											{ 
												echo 'value="' . $customer["workNumber"] . '"';
											} 											 
											elseif (isset($_POST["submit"]) && ($_POST["submit"] == $postRegisterKey))
											{
												echo 'value="' . $_POST["txtWorkPhone"] . '"';
											}
										?>
                                       
									   <?php 
											echo $isDisabled;
										?>
                                        
                                         minlength="8" maxlength="13" type="text" />
                            </div>
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                        </div>

                        <div class="row" style="margin-top: 4px">
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                            <div class="col-xs-12 col-sm-4 col-md-4">
                                <label class="label label-default" style="margin-top:4px; float: left; 
                                	font-size:0.9em" for="txtMobilePhone">Mobile Phone:</label>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <input class="form-control" style="float: left; width:100%" id="txtMobilePhone"
                                       name="txtMobilePhone" 
									   <?php 
									   		if (isset($customer)) 
											{ 
												echo 'value="' . $customer["mobileNumber"] . '"';
											} 											 
											elseif (isset($_POST["submit"]) && ($_POST["submit"] == $postRegisterKey))
											{
												echo 'value="' . $_POST["txtMobilePhone"] . '"';
											}
										?>
                                       
									   <?php 
											echo $isDisabled;
										?>
                                        
                                         minlength="9" maxlength="14" type="text" />
                            </div>
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                        </div>

                        <br/>

                        <div class="row" style="margin-top: 4px">
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                            <div class="col-xs-12 col-sm-4 col-md-4">
                                <label class="label label-default" style="margin-top:4px; float: left; 
                                	font-size:0.9em" for="txtAddress">Street Address:</label>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <input class="form-control" style="float: left; width:100%" id="txtAddress"
                                       name="txtAddress" 
									   <?php 
									   		if (isset($customer)) 
											{ 
												echo 'value="' . $customer["streetAddress"] . '"';
											} 											 
											elseif (isset($_POST["submit"]) && ($_POST["submit"] == $postRegisterKey))
											{
												echo 'value="' . $_POST["txtAddress"] . '"';
											}
										?>
                                       
									   <?php 
											echo $isDisabled;
										?>
                                        
                                         required  minlength="3" maxlength="48" type="text" />
                            </div>
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                        </div>

                        <div class="row" style="margin-top: 4px">
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                            <div class="col-xs-12 col-sm-4 col-md-4">
                                <label class="label label-default" style="margin-top:4px; float: left; font-size:0.9em" for="txtSuburb">Suburb:</label>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <input class="form-control" style="float: left; width:100%" id="txtSuburb"
                                       name="txtSuburb"
									   <?php 
									   		if (isset($customer)) 
											{ 
												echo 'value="' . $customer["suburb"] . '"';
											} 											 
											elseif (isset($_POST["submit"]) && ($_POST["submit"] == $postRegisterKey))
											{
												echo 'value="' . $_POST["txtSuburb"] . '"';
											}
										?>
                                       
									   <?php 
											echo $isDisabled;
										?>
                                        
                                         required maxlength="24" type="text" />
                            </div>
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                        </div>

                        <div class="row" style="margin-top: 4px">
                            <div class="col-xs-0 col-sm-1 col-md-2">
                            </div>
                            <div class="col-xs-12 col-sm-4 col-md-4">
                                <label class="label label-default" style="margin-top:4px; float: left; font-size:0.9em" for="txtCity">City:</label>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <input class="form-control" style="float: left; width:100%" id="txtCity"
                                       name="txtCity"
									   <?php 
									   		if (isset($customer)) 
											{ 
												echo 'value="' . $customer["city"] . '"';
											} 											 
											elseif (isset($_POST["submit"]) && ($_POST["submit"] == $postRegisterKey))
											{
												echo 'value="' . $_POST["txtCity"] . '"';
											}
										?>
                                       
									   <?php 
											echo $isDisabled;
										?>
                                        
                                         required maxlength="24" type="text" />
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
                            if (isset($_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey]) 
								&& $_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey] == 1)
                            {
                                $submitBtnText = 'Save';
								$submitValue = $postUpdateProfileKey;

								// hide a reset button, to undo changes if user cancels.
                                echo '<div class="col-xs-6 col-sm-3 col-md-3">' .
                                    '<input type="button" class="btn btn-primary" id="btnEditForm" onclick="change_form();" value="Edit" />' .
									'<input type="reset" hidden value="Reset" id="resetProfile" />' .
                                    '</div>' .
                                    '<div class="col-xs-12 col-sm-2 col-md-2">' .
									'<a class="btn btn-primary" href="../Pages/orders.php">Orders</a>' .
                                    '</div>' ;
                            }
                            else
                            {
                                $submitBtnText = 'Register';
								$submitValue = $postRegisterKey;

                                echo '<div class="col-xs-6 col-sm-3 col-md-3">' .
                                    '<input type="reset" class="btn btn-primary" value="Reset" id="resetRegister" />' .
                                    '</div>' .
                                    '<div class="col-xs-0 col-sm-2 col-md-2">' .
                                    '</div>';
                            }
                            ?>
                            <div class="col-xs-6 col-sm-3 col-md-3">
                            	<input type="text" hidden value="<?php echo $submitValue ?>" name="submit"/>
                                <input class="btn btn-primary" style="float: right;" id="submit"
                                    
                                       
									   <?php 
											echo $isDisabled;
										?>
                                        
                                         value="<?php echo $submitBtnText; ?>" type="submit" />
                            </div>
                            <div class="col-xs-0 col-sm-2 col-md-2">
								
                            </div>
                        </div>
                        <br/>
                    </div>
                    
                    </form>
                    
                    <br/>
                    <div id="divErrorMessage" hidden style="background-color: red; border: solid black 2px;">
	                    <label>
							<?php 
								// only show errors if a message is given.
								if (isset($errorMsg))
								{
									echo '<script type="text/javascript">$("#divErrorMessage").prop("hidden", false);</script>';
									echo $errorMsg;	
								}
							?>
                        </label>
                    </div>
                </div>
                <div id="divLeftSidebar" class="col-md-3">
                </div>
            </div>

        </div>
        

    

    <?php include_once("../Includes/footer.php"); ?>
</body>
</html>