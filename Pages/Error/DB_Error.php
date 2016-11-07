<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 10:16 PM
 */

include_once('../../Includes/Common.php');
include_once('../../Includes/Session.php');

$customerId = "VISITOR";
if(isset($_SESSION[\Common\SecurityConstraints::$SessionUserIdKey]))
{
    $customerId = $_SESSION[\Common\SecurityConstraints::$SessionUserIdKey];
}



if( !(isset( $_SESSION["last_Error"]) && $_SESSION["last_Error"] == "DB_connection"))
{
	header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/home.php");
	exit;
}

\Common\Logging::Log('sessionId=' . session_id() . '; customer='
    . $customerId . '; Error Message=' . $_SESSION["Error_MSG"] . "\r\n");

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quality Caps - ERROR, Database Connection</title>
    <link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../../css/Common.css">
    <script type="text/javascript" src="../../js/jquery.min.js"></script>
    <script type="text/javascript">
		// show error page for a while, then try to load home page
		function doCountdown()
		{
			var count = parseInt($("#lblCountdown").html());
			if (!count)
			{
				$("#lblCountdown").html("60");
			}
			else
			{
				count = count - 1;
				if (count <= 0)
				{
					window.location.replace("../home.php")
				}
				else
				{
					$("#lblCountdown").html(count);
				}
			}
			setTimeout(doCountdown, 1000);
		}
    	
	</script>
</head>

<body>
	<div class="container-fluid">
        <div class="row" style="margin: auto 20px">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <H3 style="color:red">
                    Could not connect to the Database
                </H3>
            </div>
        </div>
        <br/>
        <br/>
        <div class="row" style="margin: auto 20px">
            <div class="col-xs-12 col-sm-12 col-md-12">
            	<?php	
					
					if (!isset($_SESSION["Error_MSG"]))
					{
						$_SESSION["Error_MSG"] = "";	
					}
								
					$receiverEmail = \Common\Constants::$EmailAdminDefault;
					$subject = "Quality Caps ERROR, Database connection";
					$body = "An Visitor/customer attempt to open a connection to the database failed. \r\nIf DB is not down for maintenance, please check for errors.\r\n\r\n" . $_SESSION["Error_MSG"];
					
					$headers = "Content-Type: text/html; charset=TIS-620 \n";
					$headers .= "MIME-Version: 1.0 \r\n";
					
					mail($receiverEmail, $subject, $body, $headers);
					
				?>
                
                An email has been sent to the Administration team.
                
            </div>
        </div>
        
        <br/>
        <br/>
        <div class="row" style="margin: auto 20px">
            <div class="col-xs-12 col-sm-12 col-md-12">
            	You will be redirected to the home page in <label id="lblCountdown"></label> seconds.
            </div>
        </div>
    </div>
    
    <script type="text/javascript">
		doCountdown();
	</script>
    
</body>
</html>