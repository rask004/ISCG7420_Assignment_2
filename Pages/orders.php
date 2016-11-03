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

// get details required for pagination.
$pageSize = \Common\Constants::$OrdersTablePageSize;

$orderCount = $ordersManager->GetCountOfOrderSummariesByCustomer($_SESSION[\Common\SecurityConstraints::$SessionUserIdKey]);

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quality Caps - Orders</title>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/Common.css">
    <script type="text/javascript" src="../js/jquery.min.js"></script>
    <script type="text/javascript">
		// ajax function, retrieve orders page, setup pagination controls
		function ordersAjax(page)
		{
			page = parseInt(page);
			
			$("#tblOrderSummaries").load("../Includes/Ajax/Orders.ajax.php", {p:page},
				function(responseTxt, statusTxt, xhr)
				{
					if(statusTxt == "success")
					{
						var nextPage = page + 1;
						var prevPage = page - 1;
						
						var itemcount = parseInt($("#inputJsParamsOrdersItemCount").val());
						var pagesize = parseInt($("#inputJsParamsOrdersPageSize").val());	
						// only one page					
						if (itemcount <= pagesize)
						{
							$("#lblPrevPage").html("Previous");
							$("#lblPrevPage").prop("class", "label label-primary PageLinkDisabled");
							$("#lblPageNumber").html("Page: 1");
							$("#lblNextPage").html("Next");
							$("#lblNextPage").prop("class", "label label-primary PageLinkDisabled");
						}
						// showing first page
						else if (page <= 1)
						{
							$("#lblPrevPage").html("Previous");
							$("#lblPrevPage").prop("class", "label label-primary PageLinkDisabled");
							$("#lblPageNumber").html("Page: 1");
							$("#lblNextPage").html('<a href="#" class="PageLinkActive" onclick="ordersAjax( ' + nextPage + ')">Next</a>');
							$("#lblNextPage").prop("class", "label label-primary PageLinkActive");
						}
						// showing last page
						else if (page * pagesize >= itemcount)
						{
							$("#lblPrevPage").html('<a href="#" class="PageLinkActive" onclick="ordersAjax( ' + prevPage + ')">Previous</a>');
							$("#lblPrevPage").prop("class", "label label-primary PageLinkActive");
							$("#lblPageNumber").html("Page: " + page);
							$("#lblNextPage").html("Next");
							$("#lblNextPage").prop("class", "label label-primary PageLinkDisabled");
						}
						// showing between last and first page
						else
						{
							$("#lblPrevPage").html('<a href="#" class="PageLinkActive" onclick="ordersAjax( ' + prevPage + ')">Previous</a>');
							$("#lblPrevPage").prop("class", "label label-primary PageLinkActive");
							$("#lblPageNumber").html("Page: " + page);
							$("#lblNextPage").html('<a href="#" class="PageLinkActive" onclick="ordersAjax( ' + nextPage + ')">Next</a>');
							$("#lblNextSPage").prop("class", "label label-primary PageLinkActive");
						}
					}
				}
			);
		}
	</script>
</head>

<body>
    <?php
		// only members should be here - so only use members navbar.
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
                        	<label class="label label-primary" id="lblPrevPage"></label>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3">
                        	<label class="label label-primary PageLinkActive" id="lblPageNumber"></label>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3">
                        	<label class="label label-primary" id="lblNextPage"></label>
                        </div>
                    </div>
                    
                    <!-- storage of pagination details. -->
                    <input type="number" hidden id="inputJsParamsOrdersPage" value="1" />
                    <input type="number" hidden id="inputJsParamsOrdersPageSize" value="<?php echo $pageSize ?>" />
                    <input type="number" hidden id="inputJsParamsOrdersItemCount" value="<?php echo $orderCount ?>" />
                    
                </div>
                
                <br/>
                <br/>
                
                <!-- auto-sliding message to show when an order is successfully placed. -->
                <div hidden id="divCheckoutsuccessMsg" class="alert alert-success" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <strong>Success!</strong> You order has been placed!
                </div>
            </div>
            <div id="divRightSidebar" class="col-md-3">
            </div>
        </div>

    </div>
    
    <!-- show first page of orders, and set auto-slide time for successful order placement message. -->
    <script type="text/javascript">
		ordersAjax(1);
	</script>
		
    <?php include_once("../Includes/footer.php"); ?>
    
    <?php 
		// show successful order placement message if requested.
		if (isset($_REQUEST) && isset($_REQUEST['s']) && $_REQUEST['s'] = 1)
		{
			echo '<script type="text/javascript">'.
				 '$("#divCheckoutsuccessMsg").prop("hidden", false);'.
				 'window.setTimeout(function() {'.
					'$(".alert").fadeTo(500, 0).slideUp(500, function()	{'.
						'$(this).remove(); '.
					'});'.
				 '}, 4000);'.
				 '</script>';
		}
	?>
</body>
</html>

