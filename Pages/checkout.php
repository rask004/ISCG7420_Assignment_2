<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 10:18 PM
 */

include_once('../Includes/Session.php');
include_once('../Includes/Common.php');
include_once("../Includes/CapManager.php");
include_once("../Includes/OrderManager.php");

use \BusinessLayer\OrderManager;
use \BusinessLayer\CapManager;

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

// non-authenticated users should not be here.
if (!isset($_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey]) || $_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey] != 1)
{
    header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/home.php");
    exit;
}

// handle form responses
if (isset( $_POST ) && isset($_POST['submit']))
{
	if ($_POST['submit'] == 'Delete' && isset($_POST['CapId']))
	{
		//remove the cart item
		$id = $_POST['CapId'];
		if (isset($_SESSION[\Common\SecurityConstraints::$SessionCartArrayKey][$id]))
		{
			unset($_SESSION[\Common\SecurityConstraints::$SessionCartArrayKey][$id]);
		}
		
		if (count($_SESSION[\Common\SecurityConstraints::$SessionCartArrayKey]) == 0) 
		{
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/home.php");
			exit;	
		}
		
	}
	elseif($_POST['submit'] == 'Clear')
	{
		// clear the cart and return to home.
		$_SESSION[\Common\SecurityConstraints::$SessionCartArrayKey] = array();
		header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/home.php");
		exit;
	}
	elseif($_POST['submit'] == 'Checkout')
	{
		// create new order with orderitems, show successful notice, then redirect to orders page.
		$ordersManager = new OrderManager();
		$id = $_SESSION[\Common\SecurityConstraints::$SessionUserIdKey];
		$cart = $_SESSION[\Common\SecurityConstraints::$SessionCartArrayKey];
		
		$ordersManager->PlaceOrder($id, $cart);
		
		// clear the cart after placing the order.
		$_SESSION[\Common\SecurityConstraints::$SessionCartArrayKey] = array();
			 
		header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/orders.php?s=1");
    	exit;
	}
}

$capsManager = new CapManager();

$retrievedCaps = array();

$pageSize = \Common\Constants::$CheckoutTablePageSize;

$cartCount = count($_SESSION[\Common\SecurityConstraints::$SessionCartArrayKey]);

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quality Caps - Checkout</title>
    <script type="text/javascript" src="../js/jquery.min.js"></script>
    <script type="text/javascript" src="../js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/Common.css">
    <script type="text/javascript">
		function CheckoutAjaxPage(page)
		{
			page = parseInt(page);
			var pagesize = parseInt($("#inputJsParamsPageSize").val());
			var itemcount = parseInt($("#inputJsParamsItemCount").val());
			
			$("#divCheckoutContainer").load("../Includes/Ajax/CheckoutPage.ajax.php", {p:page},
				function(responseTxt, statusTxt, xhr)
				{
					if(statusTxt == "success")
					{
						var nextPage = page + 1;
						var prevPage = page - 1;
						
						
						if (itemcount <= pagesize)
						{
							$("#lblPrevPage").html("Previous");
							$("#lblPrevPage").prop("class","label label-primary PageLinkDisabled");
							$("#lblPageNumber").html("Page: 1");
							$("#lblNextPage").html("Next");
							$("#lblNextPage").prop("class","label label-primary PageLinkDisabled");
						}
						else if (page <= 1)
						{
							$("#lblPrevPage").html("Previous");
							$("#lblPrevPage").prop("class","label label-primary PageLinkDisabled");
							$("#lblPageNumber").html("Page: 1");
							$("#lblNextPage").html('<a href="#" class="PageLinkActive" onclick="CheckoutAjaxPage( ' + nextPage + ')">Next</a>');
							$("#lblNextPage").prop("class","label label-primary PageLinkActive");
						}
						else if (page * pagesize >= itemcount)
						{
							$("#lblPrevPage").html('<a href="#" onclick="CheckoutAjaxPage( ' + prevPage + ')">Previous</a>');
							$("#lblPrevPage").prop("class","label label-primary PageLinkActive");
							$("#lblPageNumber").html("Page: " + page);
							$("#lblNextPage").html("Next");
							$("#lblNextPage").prop("class","label label-primary PageLinkDisabled");
						}
						else
						{
							$("#lblPrevPage").html('<a href="#" onclick="CheckoutAjaxPage( ' + prevPage + ')">Previous</a>');
							$("#lblPrevPage").prop("class","label label-primary PageLinkActive");
							$("#lblPageNumber").html("Page: " + page);
							$("#lblNextPage").html('<a href="#" onclick="CheckoutAjaxPage( ' + nextPage + ')">Next</a>');
							$("#lblNextPage").prop("class","label label-primary PageLinkActive");
						}
						
						$("#inputJsParamsPage").val(page);
					}
				}
			);
			
		}
	</script>
</head>

<body>
	<?php
		// only members can see the orders page
        include_once("../Includes/navbar.member.php");
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
                                Checkout
                            </H3>
                        </div>
                        <div class="col-xs-0 col-sm-4 col-md-3">
                        </div>
                    </div>

                    <br/>
                    <br/>

                    <div class="row" style="margin-top: 4px">
                        <div class="container-fluid"id="divCheckoutContainer">
                                
                        </div>
                    </div>
                    
                    <br/>
                    <br/>
                    
                    <div class="row">
                        <div class="col-xs-0 col-sm-2 col-md-2">
                        </div>
                        <div class="col-xs-4 col-sm-3 col-md-3">
                            <label class="label label-primary PageLinkDisabled" id="lblPrevPage"></label>
                        </div>
                        <div class="col-xs-4 col-sm-3 col-md-3">
                            <label class="label label-primary PageLinkDisabled" id="lblPageNumber"></label>
                        </div>
                        <div class="col-xs-4 col-sm-3 col-md-3">
                            <label class="label label-primary PageLinkDisabled" id="lblNextPage"></label>
                        </div>
                    </div>
                    
                    <input type="number" hidden id="inputJsParamsPage" value="1"/>
                    <input type="number" hidden id="inputJsParamsPageSize" value="<?php echo $pageSize ?>"/>
                    <input type="number" hidden id="inputJsParamsItemCount" value="<?php echo $cartCount ?>"/>
                    
                    <br/>
                    <br/>
                    
                    <div class="row" style="margin-top: 4px">
						<div class="container-fluid">
                        	<div class="row">
                            	<div class="col-xs-0 col-sm-2 col-md-2"></div>
                            	<div class="col-xs-6 col-sm-4 col-md-4">
                                	<label>SubTotal</label>
                                </div>
                                <div class="col-xs-6 col-sm-4 col-md-4">
                                	<label id="lblCartSubTotal">$ 0.00</label>
                                </div>
                                <div class="col-xs-0 col-sm-2 col-md-2"></div>
                            </div>
                            
                            <div class="row">
                            	<div class="col-xs-0 col-sm-2 col-md-2"></div>
                            	<div class="col-xs-6 col-sm-4 col-md-4">
                                	<label>GST</label>
                                </div>
                                <div class="col-xs-6 col-sm-4 col-md-4">
                                	<label id="lblCartGst">$ 0.00</label>
                                </div>
                                <div class="col-xs-0 col-sm-2 col-md-2"></div>
                            </div>
                            
                            <div class="row">
                            	<div class="col-xs-0 col-sm-2 col-md-2"></div>
                            	<div class="col-xs-6 col-sm-4 col-md-4">
                                	<label>Full Total</label>
                                </div>
                                <div class="col-xs-6 col-sm-4 col-md-4">
                                	<label id="lblCartFullTotal">$ 0.00</label>
                                </div>
                                <div class="col-xs-0 col-sm-2 col-md-2"></div>
                            </div>                            
                        </div>
                    </div>
                    
                    <br/>
                    
                    <form method="post" enctype="multipart/form-data" autocomplete="off">
                    
                    <div class="row">
                        <div class="col-xs-0 col-sm-2 col-md-2">
                        </div>
                        <div class="col-xs-6 col-sm-3 col-md-3">
                            <input class="btn btn-primary" type="submit" Value="Clear" name="submit" />
                        </div>
                        <div class="col-xs-0 col-sm-3 col-md-3">
                        </div>
                        <div class="col-xs-6 col-sm-2 col-md-2">
                             <input id="btnCheckout" class="btn btn-primary" type="submit" Value="Checkout" name="submit" />
                        </div>
                        <div class="col-xs-0 col-sm-2 col-md-2">
                        </div>
                    </div>
                    
                    </form>
                    
                </div>
            </div>
            <div id="divRightSidebar" class="col-md-3">
            </div>
        </div>
        
        <script type="text/javascript">
            CheckoutAjaxPage(1);
        </script>

    </div>
    
	<?php 
        include_once("../Includes/footer.php"); 
    ?>
</body>
</html>

