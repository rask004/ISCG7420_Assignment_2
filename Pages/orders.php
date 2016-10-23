<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 10:17 PM
 */
 
 ini_set('display_errors','1');

include_once('../Includes/Session.php');
include('../Includes/Common.php');
include_once("../Includes/OrderManager.php");
include_once("../Includes/CapManager.php");

$ordersManager = new \BusinessLayer\OrderManager;
$capsManager = new \BusinessLayer\CapManager;

if (isset($_SESSION[\Common\Security::$SessionAuthenticationKey]) && isset($_SESSION[\Common\Security::$SessionAdminCheckKey]))
{
    header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/AdminFiles.php");
    exit;
}

// non-authenticated users should not be here.
if (!isset($_SESSION[\Common\Security::$SessionAuthenticationKey]) || $_SESSION[\Common\Security::$SessionAuthenticationKey] != 1)
{
    header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/home.php");
    exit;
}

$page_size = 5;

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quality Caps - Orders</title>
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.structure.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.theme.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/Common.css">
    <script type="text/javascript" src="../js/jquery.js"></script>
</head>

<body>
    <?php
        include_once("../Includes/navbar.member.php");
    ?>

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
                                Orders
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
                        	<table width="100%">
                            	<tr>
                                	<th>
                                    	Id
                                    </th>
									<th>
                                    	Date Placed
                                    </th>
									<th>
                                    	Status
                                    </th>
                                    <th>
                                    	Total Items
                                    </th>
                                    <th>
                                    	Total Cost
                                    </th>

                                </tr>
                                
                                <tr>
                                
                                <?php 
									
									
									// TODO: complete this
									
									$order_summaries = $ordersManager->GetAllOrderSummariesForCustomer($_SESSION[\Common\Security::$SessionUserIdKey], 0, 99);
									
									foreach($order_summaries as $summary)
									{
										echo "<td>". $summary['id'] ." </td>";
									}
									
								?>                  
                                
                                </tr>
                                
                            </table>
                        </div>
						<div class="col-xs-0 col-sm-2 col-md-2">
                        </div>
                    </div>
                    <br/>
                    <br/>
                    
                </div>
            </div>
            <div id="divRightSidebar" class="col-md-3">
                <br/>
                <?php print_r($_SESSION) ?>
                <br/>
                <?php print_r($_REQUEST) ?>
            </div>
        </div>

    </div>

    <?php include_once("../Includes/footer.php"); ?>
</body>
</html>

