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
		function CheckoutItemDelete(id)
		{
			// ajax call to script to remove item
			$.ajax("../Includes/Ajax/CheckoutPage.ajax.php", 
				{
					d:		id,
					type: 	'post',
					success:	function()
						{							
							var itemcount = $("#inputJsParamsItemCount").val() - 1;
							var pagesize = $("#inputJsParamsPageSize").val();
							var page = $("#inputJsParamsPage").val();
							
							if (page > 0 && ((page - 1) * pagesize ) >= itemcount)
							{
								page -= 1;
							}
							$("#inputJsParamsItemCount").val(itemcount);
							$("#inputJsParamsPage").val(page);
						}
				}
			);
			
			var page = $("#inputJsParamsPage").val();
			CheckoutAjaxPage(page);
			
		}
	
		function CheckoutAjaxPage(page)
		{
			var pagesize = $("#inputJsParamsPageSize").val();
			var itemcount = $("#inputJsParamsItemCount").val();
			
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
						
						
						if (itemcount <= pagesize || page <= 1)
						{
							$("#lblPrevPage").html("Previous");
							$("#lblPageNumber").html("Page: 1");
						}
						else
						{
							$("#lblPrevPage").html('<a href="#" onclick="OrdersAjax( ' + prevPage + ', ' + pagesize + ', ' + itemcount + ')">Previous</a>')
							$("#lblPageNumber").html("Page: " + page);
						}
						
						
						if (page <= 1 || (page > 1 && page * pagesize < itemcount))
						{
							$("#lblNextPage").html('<a href="#" onclick="OrdersAjax( ' + nextPage + ', ' + pagesize + ', ' + itemcount + ')">Next</a>');
						}
						else
						{
							$("#lblNextPage").html("Next");
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
                <div class="container-fluid PageSection">
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
                            <label id="lblPrevPage"></label>
                        </div>
                        <div class="col-xs-4 col-sm-2 col-md-2">
                            <label id="lblPageNumber"></label>
                        </div>
                        <div class="col-xs-4 col-sm-3 col-md-3">
                            <label id="lblNextPage"></label>
                        </div>
                        <div class="col-xs-0 col-sm-2 col-md-2">
                        </div>
                    </div>
                    
                    <input type="number" hidden id="inputJsParamsPage" value="1"/>
                    <input type="number" hidden id="inputJsParamsPageSize" value="<?php echo $page_size ?>"/>
                    <input type="number" hidden id="inputJsParamsItemCount" value="<?php echo $cart_count ?>"/>
                    
                </div>
            </div>
            <div id="divRightSidebar" class="col-md-3">
	            <br/>
                <?php print_r($_SESSION); ?>
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

