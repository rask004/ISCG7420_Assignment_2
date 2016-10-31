<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 10:17 PM
 */

include_once('../Includes/Session.php');
include_once('../Includes/Common.php');
include_once("../Includes/OrderManager.php");

$ordersManager = new \BusinessLayer\OrderManager;

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

$page_size = \Common\Constants::$OrdersTablePageSize;

$order_count = $ordersManager->GetCountOfOrderSummariesByCustomer($_SESSION[\Common\Security::$SessionUserIdKey]);

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
    <script type="text/javascript">
		function OrdersAjax(page, pagesize, itemcount)
		{
			$("#tblOrderSummaries").load("../Includes/Ajax/Orders.ajax.php", {p:page},
				function(responseTxt, statusTxt, xhr)
				{
					if(statusTxt == "success")
					{
						var nextPage = page + 1;
						var prevPage = page - 1;
						
						
						if (itemcount <= pagesize)
						{
							$("#lblPrevPage").html("Previous");
							$("#lblPrevPage").css("PageLinkDisabled");
							$("#lblPageNumber").html("Page: 1");
							$("#lblNextPage").html("Next");
							$("#lblNextPage").css("PageLinkDisabled");
						}
						else if (page <= 1)
						{
							$("#lblPrevPage").html("Previous");
							$("#lblPrevPage").css("PageLinkDisabled");
							$("#lblPageNumber").html("Page: 1");
							$("#lblNextPage").html('<a href="#" class="PageLinkActive" onclick="OrdersAjax( ' + nextPage + ', ' + pagesize + ', ' + itemcount + ')">Next</a>');
							$("#lblNextPage").css("PageLinkActive");
						}
						else if (page * pagesize >= itemcount)
						{
							$("#lblPrevPage").html('<a href="#" class="PageLinkActive" onclick="OrdersAjax( ' + prevPage + ', ' + pagesize + ', ' + itemcount + ')">Previous</a>');
							$("#lblPrevPage").css("PageLinkActive");
							$("#lblPageNumber").html("Page: " + page);
							$("#lblNextPage").html("Next");
							$("#lblNextPage").css("PageLinkDisabled");
						}
						else
						{
							$("#lblPrevPage").html('<a href="#" class="PageLinkActive" onclick="OrdersAjax( ' + prevPage + ', ' + pagesize + ', ' + itemcount + ')">Previous</a>');
							$("#lblPrevPage").css("PageLinkActive");
							$("#lblPageNumber").html("Page: " + page);
							$("#lblNextPage").html('<a href="#" class="PageLinkActive" onclick="OrdersAjax( ' + nextPage + ', ' + pagesize + ', ' + itemcount + ')">Next</a>');
							$("#lblNextSPage").css("PageLinkActive");
						}
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
                        	<table width="100%" class="table table-striped" id="tblOrderSummaries">
                                
                            </table>
                        </div>
						<div class="col-xs-0 col-sm-2 col-md-2">
                        </div>
                    </div>
                    <br/>
                    <br/>
                    <div class="row">
                    	<div class="col-xs-0 col-sm-1 col-md-1">
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-4">
                        	<label class="label label-primary PageLinkDisabled" id="lblPrevPage"></label>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3">
                        	<label class="label label-primary PageLinkDisabled" id="lblPageNumber"></label>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3">
                        	<label class="label label-primary PageLinkDisabled" id="lblNextPage"></label>
                        </div>
                    </div>
                    
                </div>
                
                <br/>
                <br/>
                
                <div hidden id="divCheckoutsuccessMsg" class="alert alert-success" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <strong>Success!</strong> You order has been placed!
                </div>
            </div>
            <div id="divRightSidebar" class="col-md-3">
            </div>
        </div>

    </div>
    
    <script type="text/javascript">
		OrdersAjax(1, <?php echo $page_size ?>, <?php echo $order_count ?> );
		window.setTimeout(function() 
			{
				$(".alert").fadeTo(500, 0).slideUp(500, function()
				{
					$(this).remove(); 
				});
			}, 4000);
	</script>
		
    <?php include_once("../Includes/footer.php"); ?>
    
    <?php 
		if (isset($_REQUEST) && isset($_REQUEST['s']) && $_REQUEST['s'] = 1)
		{
			echo '<script type="text/javascript">$("#divCheckoutsuccessMsg").prop("hidden", false);</script>';
		}
	?>
</body>
</html>

