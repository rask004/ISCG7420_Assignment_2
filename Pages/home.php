<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 10:19 PM
 */

include_once('../Includes/Session.php');
include_once('../Includes/Common.php');
include_once('../Includes/CategoryManager.php');
include_once('../Includes/CapManager.php');

// only customers and visitors can visit home page. 
if (isset($_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey]) && isset($_SESSION[\Common\SecurityConstraints::$SessionAdminCheckKey]))
{
    header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/AdminFiles.php");
    exit;
}

$cartPageSize = \Common\Constants::$HomeCartTablePageSize;
$cartItemCount = count($_SESSION[\Common\SecurityConstraints::$SessionCartArrayKey]);

$categoryPageSize = \common\constants::$HomeCategoriesTablePageSize;
$categoryManager = new \BusinessLayer\CategoryManager;
$categoryCount = $categoryManager->RetrieveCountOfCategoriesForHomePage();

$capPageSize = \common\constants::$HomeCapsTablePageSize;

unset($categoryManager);

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
			$( document ).ready(function() {
				$("#divShoppingCart").load("../Includes/Ajax/HomeCart.ajax.php", {c:1, p:1},
					function(responseTxt, statusTxt, xhr)
					{
						$("#inputJsParamsCartItemCount").val(0);	// cart is now empty.
						$("#inputJsParamsCartPage").val(1);			// placeholder for current page.
						
						$("#lblCartPrevPage").html("Previous");
						$("#lblCartPrevPage").prop("class", "label label-primary PageLinkDisabled");
						$("#lblCartPageNumber").html("Page: 1");
						$("#lblCartNextPage").html("Next");
						$("#lblCartNextPage").prop("class", "label label-primary PageLinkDisabled");
					}
				);
			});
		};
		
		// manage the cart page controls.
		function UpdateCartPageControls(page)
		{
			page = parseInt(page);
			var pagesize =  parseInt($("#inputJsParamsCartPageSize").val());
			var itemcount = parseInt($("#inputJsParamsCartItemCount").val());
			
			// update page controls.
			var nextPage = page + 1;
			var prevPage = page - 1;
			
			if (itemcount <= pagesize)
			{
				$("#lblCartPrevPage").html("Previous");
				$("#lblCartPrevPage").prop("class", "label label-primary PageLinkDisabled");
				$("#lblCartPageNumber").html("Page: 1");
				$("#lblCartNextPage").html("Next");
				$("#lblCartNextPage").prop("class", "label label-primary PageLinkDisabled");
			}
			else if (page <= 1)
			{
				$("#lblCartPrevPage").html("Previous");
				$("#lblCartPrevPage").prop("class", "label label-primary PageLinkDisabled");
				$("#lblCartPageNumber").html("Page: 1");
				$("#lblCartNextPage").html('<a href="#" class="PageLinkActive" onclick="ShowPageCart( ' + nextPage + ')">Next</a>');
				$("#lblCartNextPage").prop("class", "label label-primary PageLinkActive");
			}
			else if (page * pagesize >= itemcount)
			{
				$("#lblCartPrevPage").html('<a href="#" class="PageLinkActive" onclick="ShowPageCart( ' + prevPage + ')">Previous</a>');
				$("#lblCartPrevPage").prop("class", "label label-primary PageLinkActive");
				$("#lblCartPageNumber").html("Page: " + page);
				$("#lblCartNextPage").html("Next");
				$("#lblCartNextPage").prop("class", "label label-primary PageLinkDisabled");
			}
			else
			{
				$("#lblCartPrevPage").html('<a href="#" class="PageLinkActive" onclick="ShowPageCart( ' + prevPage + ')">Previous</a>');
				$("#lblCartPrevPage").prop("class", "label label-primary PageLinkActive");
				$("#lblCartPageNumber").html("Page: " + page);
				$("#lblCartNextPage").html('<a href="#" class="PageLinkActive" onclick="ShowPageCart( ' + nextPage + ')">Next</a>');
				$("#lblCartNextPage").prop("class", "label label-primary PageLinkActive");
			}
			
		}
		
		// show a page of the cart.
		function ShowPageCart(page)
		{			
			$( document ).ready(function() {
				// update the current page.				
			
				$("#divShoppingCart").load("../Includes/Ajax/HomeCart.ajax.php", {p:page},
					function(responseTxt, statusTxt, xhr)
					{
						if(statusTxt == "success")
						{
							UpdateCartPageControls(page);
						}
					}
				);
			});
			
			// store current page number for other controls to use.
			$("#inputJsParamsCartPage").val(page);			
		};
		
		// remove one cart item, given an item id.
		function DeleteCartItem(id)
		{
			id = parseInt(id);
			var page = parseInt($("#inputJsParamsCartPage").val());
			
			$( document ).ready(function() {
				// both delete item AND show page of items.
				$("#divShoppingCart").load("../Includes/Ajax/HomeCart.ajax.php", {d:id, p:page},
					function(responseTxt, statusTxt, xhr)
					{
						if(statusTxt == "success")
						{
							var itemcount = $("#inputJsParamsCartItemCount").val() - 1;
							$("#inputJsParamsCartItemCount").val(itemcount);
							if (($("#inputJsParamsCartPage").val() - 1) * $("#inputJsParamsCartPageSize").val() >= $("#inputJsParamsCartItemCount").val())
							{
								var page = $("#inputJsParamsCartPage").val() - 1;
								$("#inputJsParamsCartPage").val(page);	
							}
							
							$("#inputJsParamsCartPage").val(page);
							$("#inputJsParamsCartItemCount").val(itemcount);
							
							UpdateCartPageControls(page);
						}
					}
				);
			});
		};
		
		// show a page of categories
		function ShowPageCategories(page)
		{
			// grab and parse stored page data.
			page = parseInt(page);
			
			$( document ).ready(function() {
				// update the current page.
				$("#divAvailableCategories").load("../Includes/Ajax/HomeCategories.ajax.php", {p:page},
					function(responseTxt, statusTxt, xhr)
					{
						if(statusTxt == "success")
						{
							// update page controls.
							var nextPage = page + 1;
							var prevPage = page - 1;
							
							if ($("#inputJsParamsCategoryItemCount").val() <= $("#inputJsParamsCategoryPageSize").val())
							{
								$("#lblCategoriesPrevPage").html("Previous");
								$("#lblCategoriesPrevPage").prop("class", "label label-primary PageLinkDisabled");
								$("#lblCategoriesPageNumber").html("Page: 1");
								$("#lblCategoriesNextPage").html("Next");
								$("#lblCategoriesNextPage").prop("class", "label label-primary PageLinkDisabled");
							}
							else if (page <= 1)
							{
								$("#lblCategoriesPrevPage").html("Previous");
								$("#lblCategoriesPrevPage").prop("class", "label label-primary PageLinkDisabled");
								$("#lblCategoriesPageNumber").html("Page: 1");
								$("#lblCategoriesNextPage").html('<a href="#" class="PageLinkActive" onclick="ShowPageCategories( ' + nextPage + ')">Next</a>');
								$("#lblCategoriesNextPage").prop("class", "label label-primary PageLinkActive");
							}
							else if (page * $("#inputJsParamsCategoryPageSize").val() >= $("#inputJsParamsCategoryItemCount").val())
							{
								$("#lblCategoriesPrevPage").html('<a href="#" class="PageLinkActive" onclick="ShowPageCategories( ' + prevPage + ')">Previous</a>');
								$("#lblCategoriesPrevPage").prop("class", "label label-primary PageLinkActive");
								$("#lblCategoriesPageNumber").html("Page: " + page);
								$("#lblCategoriesNextPage").html("Next");
								$("#lblCategoriesNextPage").prop("class", "label label-primary PageLinkDisabled");
							}
							else
							{
								$("#lblCategoriesPrevPage").html('<a href="#" class="PageLinkActive" onclick="ShowPageCategories( ' + prevPage + ')">Previous</a>');
								$("#lblCategoriesPrevPage").prop("class", "label label-primary PageLinkActive");
								$("#lblCategoriesPageNumber").html("Page: " + page);
								$("#lblCategoriesNextPage").html('<a href="#" class="PageLinkActive" onclick="ShowPageCategories( ' + nextPage + ')">Next</a>');
								$("#lblCategoriesNextPage").prop("class", "label label-primary PageLinkActive");
							}
						}
					}
				);
			});
			
			// store current page number for other controls to use.
			$("#inputJsParamsCategoryPage").val(page);
		};
		
		// show a page of caps, given a categoryId. If categoryId is -1, show page of all caps.
		function ShowPageCaps(catId, page)
		{
			// grab and parse stored page data.
			catId = parseInt(catId);
			page = parseInt(page);
			
			$("#divCapsPageControls").prop("hidden", false);
			$("#divCapsByCategory").prop("hidden", false);
			$("#divCapDetails").prop("hidden", true);
			$("#txtCapsHeader").html("Caps");
			
			$( document ).ready(function() {
				// update the current page.
				$("#divCapsByCategory").load("../Includes/Ajax/HomeCaps.ajax.php", {c:catId, p:page},
					function(responseTxt, statusTxt, xhr)
					{
						if(statusTxt == "success")
						{
							var pagesize =  parseInt($("#inputJsParamsCapPageSize").val());
							var itemcount = parseInt($("#inputJsParamsCapItemCount").val());
														
							// update page controls.
							var nextPage = page + 1;
							var prevPage = page - 1;
							
							if (itemcount <= pagesize)
							{
								$("#lblCapsPrevPage").html("Previous");
								$("#lblCapsPrevPage").prop("class", "label label-primary PageLinkDisabled");
								$("#lblCapsPageNumber").html("Page: 1");
								$("#lblCapsNextPage").html("Next");
								$("#lblCapsNextPage").prop("class", "label label-primary PageLinkDisabled");
							}
							else if (page <= 1)
							{
								$("#lblCapsPrevPage").html("Previous");
								$("#lblCapsPrevPage").prop("class", "label label-primary PageLinkDisabled");
								$("#lblCapsPageNumber").html("Page: 1");
								$("#lblCapsNextPage").html('<a href="#" class="PageLinkActive" onclick="ShowPageCaps( ' + catId + ',' + nextPage + ')">Next</a>');
								$("#lblCapsNextPage").prop("class", "label label-primary PageLinkActive");
							}
							else if (page * pagesize >= itemcount)
							{
								$("#lblCapsPrevPage").html('<a href="#" class="PageLinkActive" onclick="ShowPageCaps( ' + catId + ',' + prevPage + ')">Previous</a>');
								$("#lblCapsPrevPage").prop("class", "label label-primary PageLinkActive");
								$("#lblCapsPageNumber").html("Page: " + page);
								$("#lblCapsNextPage").html("Next");
								$("#lblCapsNextPage").prop("class", "label label-primary PageLinkDisabled");
							}
							else
							{
								$("#lblCapsPrevPage").html('<a href="#" class="PageLinkActive" onclick="ShowPageCaps( ' + catId + ',' + prevPage + ')">Previous</a>');
								$("#lblCapsPrevPage").prop("class", "label label-primary PageLinkActive");
								$("#lblCapsPageNumber").html("Page: " + page);
								$("#lblCapsNextPage").html('<a href="#" class="PageLinkActive" onclick="ShowPageCaps( ' + catId + ',' + nextPage + ')">Next</a>');
								$("#lblCapsNextPage").prop("class", "label label-primary PageLinkActive");
							}
						}
					}
				);
			});
		};
		
		// show a page of caps, given a categoryId. If categoryId is -1, show page of all caps.
		function ShowCapDetails(capId)
		{
			// grab and parse stored page data.
			capId = parseInt(capId);
			
			$( document ).ready(function() {
				// update the current page.
				$("#divCapDetails").load("../Includes/Ajax/HomeCaps.ajax.php", {d:capId},
					function(responseTxt, statusTxt, xhr)
					{
						$("#divCapsPageControls").prop("hidden", true);
						$("#divCapsByCategory").prop("hidden", true);
						$("#divCapDetails").prop("hidden", false);
						$("#txtCapsHeader").html("Cap Details");
					}
				);
			});
		};
		
		// add a cap to the cart
		function AddCapToCart()
		{
			var capId = parseInt($("#lblAddCapId").html());
			var qty = parseInt($("#inputAddCapQuantity").val());
			var page = parseInt($("#inputJsParamsCartPage").val());
			
			$( document ).ready(function() {
				$("#divShoppingCart").load("../Includes/Ajax/HomeCart.ajax.php", {a:capId, aq:qty, p:page},
					function(responseTxt, statusTxt, xhr)
					{
						$("#divCapsPageControls").prop("hidden", false);
						$("#divCapsByCategory").prop("hidden", false);
						$("#divCapDetails").prop("hidden", true);
						$("#txtCapsHeader").html("Caps");
						
						if(statusTxt == "success")
						{
							UpdateCartPageControls(page);
						}
					}
				);
			});
		}
		
		// reshow the caps page
		function ReturnToCapListing()
		{
			$("#divCapsPageControls").prop("hidden", false);
			$("#divCapsByCategory").prop("hidden", false);
			$("#divCapDetails").prop("hidden", true);
			$("#txtCapsHeader").html("Caps");
		}
		
	</script>
</head>

<body>
    <?php
    if (isset($_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey]) && $_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey] == 1)
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
                <div class="container-fluid panel panel-default PageSection">
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
                    
                    <br/>
                    
                    <div class="row">
                    	<div class="col-xs-12 col-sm-12 col-md-12">
                    		<div class="container-fluid">
                        
                                <div class="row">
                                    <div class="col-xs-0 col-sm-1 col-md-1">
                                    </div>
                                    <div class="col-xs-4 col-sm-3 col-md-3">
                                        <label class="label label-primary" id="lblCategoriesPrevPage"></label>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <label class="label label-primary PageLinkDisabled" id="lblCategoriesPageNumber"></label>
                                    </div>
                                    <div class="col-xs-4 col-sm-3 col-md-3">
                                        <label class="label label-primary" id="lblCategoriesNextPage"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <input type="number" hidden id="inputJsParamsCategoryPage" value="1" />
                    <input type="number" hidden id="inputJsParamsCategoryPageSize" value="<?php echo $categoryPageSize ?>" />
                    <input type="number" hidden id="inputJsParamsCategoryItemCount" value="<?php echo $categoryCount ?>" />
                    
                    <br/>
                    
                </div>
            </div>
            <div id="divCentreSpace" class="col-md-6">
                <div class="container-fluid panel panel-default PageSection">
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
						<div class="container-fluid" id="divCapsByCategory">
                        	<!-- create table list of Products for a selected category, paginated -->
                        </div>
						<div hidden class="container-fluid" id="divCapDetails">
                        	<!-- show details of a product, option to add to shopping cart. -->
                        </div>
                    </div>
                    <br/>
                    
                    <div id="divCapsPageControls" class="row">
                        
                        <div class="col-xs-12 col-sm-12 col-md-12">
                        	<div class="container-fluid">
                                <div class="row">
                                    <div class="col-xs-0 col-sm-1 col-md-1">
                                    </div>
                                    <div class="col-xs-4 col-sm-3 col-md-3">
                                        <label class="label label-primary" id="lblCapsPrevPage"></label>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <label class="label label-primary PageLinkDisabled" id="lblCapsPageNumber"></label>
                                    </div>
                                    <div class="col-xs-4 col-sm-3 col-md-3">
                                        <label class="label label-primary" id="lblCapsNextPage"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                              
                        
                    <input type="number" hidden id="inputJsParamsCapPage" value="1" />
                    <input type="number" hidden id="inputJsParamsCapPageSize" value="<?php echo $capPageSize ?>" />
                    <input type="number" hidden id="inputJsParamsCapItemCount" value="" />
                    
                    <br/>
                        
                </div>
            </div>
            <div id="divRightSidebar" class="col-md-3">
                <div class="container-fluid panel panel-default PageSection">
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
                            <input type="number" hidden id="inputJsParamsCartItemCount" value="<?php echo $cartItemCount ?>" />
                        </div>
                    </div>
                    
                    <br/>
                    
                    <div class="row">
                        <div class="container-fluid">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="row">
                                    <div class="col-xs-0 col-sm-1 col-md-1">
                                        
                                    </div>
                                    <div class="col-xs-4 col-sm-3 col-md-3">
                                        <label class="label label-primary" id="lblCartPrevPage"></label>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <label class="label label-primary PageLinkDisabled" id="lblCartPageNumber"></label>
                                    </div>
                                    <div class="col-xs-4 col-sm-3 col-md-3">
                                        <label class="label label-primary" id="lblCartNextPage"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <input type="number" hidden id="inputJsParamsCartPage" value="1" />
                    <input type="number" hidden id="inputJsParamsCartPageSize" value="<?php echo $cartPageSize ?>" />
                    
                    <br/>
                    
                    <div class="row" style="margin-top: 4px">
                    	<!-- show buttons for clearing cart and doing checkout, checkout only available if logged in. -->
                        <div class="col-xs-0 col-sm-1 col-md-1"></div>
                        <div class="col-xs-6 col-sm-3 col-md-3">
                        	<input type="button" class="btn btn-primary" value="Clear" onclick="ClearCart()" />
                        </div>
                        <div class="col-xs-0 col-sm-3 col-md-3"></div>
                        <div class="col-xs-6 col-sm-3 col-md-3">
                        	<input disabled id="btnCheckout" type="button" class="btn btn-primary" value="Checkout" 
                            	onclick="location.assign('http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/checkout.php')" />
                        </div>
                    </div>
                    <br/> 
                </div>
            </div>
        </div>
    </div>
    
    <script type="text/javascript">
		// load initial pages for each section.
    	ShowPageCart(1);
		ShowPageCategories(1);
		ShowPageCaps(-1,1);		// initially, load all caps. 
    </script>

    <?php include_once("../Includes/footer.php"); ?>
    
    <?php
		// enable or disable checkout button depending on if customer is logged in or there are items in the cart.
		if(count($_SESSION[\Common\SecurityConstraints::$SessionCartArrayKey]) == 0)
		{
			echo '<script type="text/javascript">$("#btnCheckout").prop("disabled", true);</script>';
		}
		elseif (isset($_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey]) 
				&& $_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey] == 1)
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

