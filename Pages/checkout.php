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

// handle form responses
if (isset( $_POST ) && isset($_POST['submit']))
{
	if ($_POST['submit'] == 'Delete' && isset($_POST['CapId']))
	{
		//remove the cart item
		$id = $_POST['CapId'];
		if (isset($_SESSION[\Common\Security::$SessionCartArrayKey][$id]))
		{
			unset($_SESSION[\Common\Security::$SessionCartArrayKey][$id]);
		}
		
	}
	elseif($_POST['submit'] == 'Clear')
	{
		// clear the cart and return to home.
		unset($_SESSION[\Common\Security::$SessionCartArrayKey]);
		$_SESSION[\Common\Security::$SessionCartArrayKey] = array();
		header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/home.php");
		exit;
	}
	elseif($_POST['submit'] == 'Checkout')
	{
		// create new order with orderitems, show successful notice, then redirect to orders page.
		$ordersManager = new \BusinessLayer\OrderManager;
		$id = $_SESSION[\Common\Security::$SessionUserIdKey];
		$cart = $_SESSION[\Common\Security::$SessionCartArrayKey];
		
		$ordersManager->PlaceOrder($id, $cart);
		
		// clear the cart after placing the order.
		$_SESSION[\Common\Security::$SessionCartArrayKey] = array();
			 
		header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/orders.php?s=1");
    	exit;
	}
}

$capsManager = new \BusinessLayer\CapManager;

$retrievedCaps = array();

$page_size = \Common\Constants::$CheckoutTablePageSize;

$cart_count = count($_SESSION[\Common\Security::$SessionCartArrayKey]);




?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quality Caps - Checkout</title>
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.structure.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.theme.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/Common.css">
    <script type="text/javascript" src="../js/jquery.js"></script>
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
							$("#lblPageNumber").html("Page: 1");
							$("#lblNextPage").html("Next");
						}
						else if (page <= 1)
						{
							$("#lblPrevPage").html("Previous");
							$("#lblPageNumber").html("Page: 1");
							$("#lblNextPage").html('<a href="#" onclick="CheckoutAjaxPage( ' + nextPage + ')">Next</a>');
						}
						else if (page * pagesize >= itemcount)
						{
							$("#lblPrevPage").html('<a href="#" onclick="CheckoutAjaxPage( ' + prevPage + ')">Previous</a>')
							$("#lblPageNumber").html("Page: " + page);
							$("#lblNextPage").html("Next");
						}
						else
						{
							$("#lblPrevPage").html('<a href="#" onclick="CheckoutAjaxPage( ' + prevPage + ')">Previous</a>')
							$("#lblPageNumber").html("Page: " + page);
							$("#lblNextPage").html('<a href="#" onclick="CheckoutAjaxPage( ' + nextPage + ')">Next</a>');
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
                    <input type="number" hidden id="inputJsParamsPageSize" value="<?php echo $page_size ?>"/>
                    <input type="number" hidden id="inputJsParamsItemCount" value="<?php echo $cart_count ?>"/>
                    
                    <br/>
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
                             <input class="btn btn-primary" type="submit" Value="Checkout" name="submit" />
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

