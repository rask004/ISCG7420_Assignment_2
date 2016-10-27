<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 10:19 PM
 */

include_once('../Includes/Session.php');
include_once('../Includes/Common.php');

// only customers and visitors can visit home page. 
if (isset($_SESSION[\Common\Security::$SessionAuthenticationKey]) && isset($_SESSION[\Common\Security::$SessionAdminCheckKey]))
{
    header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/AdminFiles.php");
    exit;
}

$cartPageSize = \Common\Constants::$HomeCartTablePageSize;
$cartItemCount = count($_SESSION[\Common\Security::$SessionCartArrayKey]);

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quality Caps - Home Page</title>
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.structure.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.theme.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/Common.css">
    <script type="text/javascript" src="../js/jquery.js"></script>
    <script type="text/javascript">
		// clear the cart. 
		function ClearCart()
		{
			$("#divShoppingCart").load("../Includes/Ajax/HomeCart.ajax.php", {c:1},
				function(responseTxt, statusTxt, xhr)
				{
					$("#inputJsParamsCartItemCount").val(0);	// cart is now empty.
					$("#inputJsParamsCartPage").val(1);			// placeholder for current page.
					$("#lblCartPrevPage").html("");
					$("#lblCartPageNumber").html("");			// no cart items, so no pages to paginate.
					$("#lblCartNextPage").html("");
				}
			);
		};
		
		function ShowPageCart(page)
		{
			// grab and parse stored page data.
			page = parseInt(page);
			var pagesize = parseInt($("#inputJsParamsCartPageSize").val());
			var itemcount = parseInt($("#inputJsParamsCartItemCount").val());
			
			// update the current cart page.
			$("#divShoppingCart").load("../Includes/Ajax/HomeCart.ajax.php", {p:page},
				function(responseTxt, statusTxt, xhr)
				{
					if(statusTxt == "success")
					{
						// update page controls.
						var nextPage = page + 1;
						var prevPage = page - 1;
						
						if (itemcount <= pagesize)
						{
							$("#lblCartPrevPage").html("Previous");
							$("#lblCartPageNumber").html("Page: 1");
							$("#lblCartNextPage").html("Next");
						}
						else if (page <= 1)
						{
							$("#lblCartPrevPage").html("Previous");
							$("#lblCartPageNumber").html("Page: 1");
							$("#lblCartNextPage").html('<a href="#" onclick="ShowPageCart( ' + nextPage + ')">Next</a>');
						}
						else if (page * pagesize >= itemcount)
						{
							$("#lblCartPrevPage").html('<a href="#" onclick="ShowPageCart( ' + prevPage + ')">Previous</a>')
							$("#lblCartPageNumber").html("Page: " + page);
							$("#lblCartNextPage").html("Next");
						}
						else
						{
							$("#lblCartPrevPage").html('<a href="#" onclick="ShowPageCart( ' + prevPage + ')">Previous</a>')
							$("#lblCartPageNumber").html("Page: " + page);
							$("#lblCartNextPage").html('<a href="#" onclick="ShowPageCart( ' + nextPage + ')">Next</a>');
						}
					}
				}
			);
			
			// store current page number for other controls to use.
			$("#inputJsParamsCartPage").val(page);
		};
		
		function DeleteCartItem(id)
		{
			id = parseInt(id);
			var page = parseInt($("#inputJsParamsCartPage").val());
			var pagesize = parseInt($("#inputJsParamsCartPageSize").val());
			var itemcount = parseInt($("#inputJsParamsCartItemCount").val());
			
			$("#divShoppingCart").load("../Includes/Ajax/HomeCart.ajax.php", {d:id, p:page},
				function(responseTxt, statusTxt, xhr)
				{
					if(statusTxt == "success")
					{
						itemcount -= 1;
						if ((page - 1) * pagesize >= itemcount)
						{
							page -= 1;	
						}
						
						var nextPage = page + 1;
						var prevPage = page - 1;
						
						
						if (itemcount <= pagesize)
						{
							$("#lblCartPrevPage").html("Previous");
							$("#lblCartPageNumber").html("Page: 1");
							$("#lblCartNextPage").html("Next");
						}
						else if (page <= 1)
						{
							$("#lblCartPrevPage").html("Previous");
							$("#lblCartPageNumber").html("Page: 1");
							$("#lblCartNextPage").html('<a href="#" onclick="ShowPageCart( ' + nextPage + ')">Next</a>');
						}
						else if (page * pagesize >= itemcount)
						{
							$("#lblCartPrevPage").html('<a href="#" onclick="ShowPageCart( ' + prevPage + ')">Previous</a>')
							$("#lblCartPageNumber").html("Page: " + page);
							$("#lblCartNextPage").html("Next");
						}
						else
						{
							$("#lblCartPrevPage").html('<a href="#" onclick="ShowPageCart( ' + prevPage + ')">Previous</a>')
							$("#lblCartPageNumber").html("Page: " + page);
							$("#lblCartNextPage").html('<a href="#" onclick="ShowPageCart( ' + nextPage + ')">Next</a>');
						}
					}
				}
			);
			
			$("#inputJsParamsCartPage").val(page);
			$("#inputJsParamsCartItemCount").val(itemcount);
		};
		
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

    <div class="container-fluid PageContainer">

        <div class="row">
            <div id="divLeftSidebar" class="col-md-3">
                <div class="container-fluid PageSection">
                    <br/>

                    <div class="row" style="margin: auto 20px">
                        <div class="col-xs-0 col-sm-4 col-md-3">
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-6 DecoHeader">
                            <H3>
                                Categories
                            </H3>
                        </div>
                        <div class="col-xs-0 col-sm-4 col-md-3">
                        </div>
                    </div>

                    <br/>
                    <br/>

                    <div class="row" style="margin-top: 4px">
						<div class="container-fluid" id="divAvailableCategories">
                        	<!-- create flow list of Categories having products, paginated -->
                        </div>
                    </div>
                </div>
            </div>
            <div id="divCentreSpace" class="col-md-6">
                <div class="container-fluid PageSection">
                    <br/>

                    <div class="row" style="margin: auto 20px">
                        <div class="col-xs-0 col-sm-4 col-md-3">
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-6 DecoHeader">
                            <H3 id="txtCapsHeader">
                                Caps
                            </H3>
                        </div>
                        <div class="col-xs-0 col-sm-4 col-md-3">
                        </div>
                    </div>

                    <br/>
                    <br/>

                    <div class="row" style="margin-top: 4px">
						<div class="container-fluid" id="divProductsByCategory">
                        	<!-- create table list of Products for a selected category, paginated -->
                        </div>
						<div hidden class="container-fluid" id="divProductDetails">
                        	<!-- show details of a product, option to add to shopping cart. -->
                        </div>
                    </div>
                </div>
            </div>
            <div id="divRightSidebar" class="col-md-3">
                <div class="container-fluid PageSection">
                    <br/>

                    <div class="row" style="margin: auto 20px">
                        <div class="col-xs-0 col-sm-4 col-md-3">
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-6 DecoHeader">
                            <H3>
                                Shopping Cart
                            </H3>
                        </div>
                        <div class="col-xs-0 col-sm-4 col-md-3">
                        </div>
                    </div>

                    <br/>
                    <br/>

                    <div class="row" style="margin-top: 4px">
						<div class="container-fluid" id="divShoppingCart">
                        	<!-- create flow list of items in cart, paginated, with totals at bottom -->
                        </div>
                    </div>
                    
                    <br/>
                    
                    <div class="row">
                        <div class="col-xs-0 col-sm-2 col-md-2">
                        </div>
                        <div class="col-xs-4 col-sm-3 col-md-3">
                            <label id="lblCartPrevPage"></label>
                        </div>
                        <div class="col-xs-4 col-sm-2 col-md-2">
                            <label id="lblCartPageNumber"></label>
                        </div>
                        <div class="col-xs-4 col-sm-3 col-md-3">
                            <label id="lblCartNextPage"></label>
                        </div>
                        <div class="col-xs-0 col-sm-2 col-md-2">
                        </div>
                    </div>
                    
                    <input type="number" hidden id="inputJsParamsCartPage" value="1" />
                    <input type="number" hidden id="inputJsParamsCartPageSize" value="<?php echo $cartPageSize ?>" />
                    <input type="number" hidden id="inputJsParamsCartItemCount" value="<?php echo $cartItemCount ?>" />
                    
                    <br/>
                    
                    <div class="row" style="margin-top: 4px">
                    	<!-- show buttons for clearing cart and doing checkout, checkout only available if logged in. -->
                        <div class="col-xs-0 col-sm-1 col-md-1"></div>
                        <div class="col-xs-6 col-sm-3 col-md-3">
                        	<input type="button" value="Clear" />
                        </div>
                        <div class="col-xs-0 col-sm-3 col-md-3"></div>
                        <div class="col-xs-6 col-sm-4 col-md-4">
                        	<input disabled id="btnCheckout" type="button" value="Checkout" onclick="location.assign('http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/checkout.php')" />
                        </div>
                    </div>
                    <br/> 
                </div>
            </div>
        </div>

    </div>
    
    <script type="text/javascript">
    	ShowPageCart(1);
    </script>

    <?php include_once("../Includes/footer.php"); ?>
    
    <?php
		if(count($_SESSION[\Common\Security::$SessionCartArrayKey]) == 0)
		{
			echo '<script type="text/javascript">$("#btnCheckout").prop("disabled", true);</script>';
		}
		elseif (isset($_SESSION[\Common\Security::$SessionAuthenticationKey]) && $_SESSION[\Common\Security::$SessionAuthenticationKey] == 1)
		{
			echo '<script type="text/javascript">$("#btnCheckout").prop("disabled", false);</script>';
		}
		else
		{
			echo '<script type="text/javascript">$("#btnCheckout").prop("disabled", true);</script>';
		}
	?>
</body>
</html>

