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
			//		include a followup function, to decrement itemcount, decrement page if too high, and update the Checkout table and pages.
		}
	
		function CheckoutAjaxPage(page, pagesize, itemcount)
		{
			// update checkout container with current page of items, and update paging elements
			
			var nextPage = page + 1;
			var prevPage = page - 1;
			
			// only 1 pages worth of items to show
			if (itemcount <= pagesize)
			{
				$("#lblPrevPage").html("Previous");
				$("#lblPageNumber").html("Page: 1");
				$("#lblNextPage").html("Next");
				$("#divCheckoutContainer").load("../Includes/Ajax/CheckoutPage.ajax.php", {p:page});
						
				
			}			
			// showing first set of order items
			else if (page <= 1)
			{
				$("#lblPrevPage").html("Previous");
				$("#lblPageNumber").html("Page: 1");
				$("#lblNextPage").html('<a href="#" onclick="CheckoutAjaxPage( ' + nextPage + ', ' + pagesize + ', ' + itemcount + ')">Next</a>');
				$("#divCheckoutContainer").load("../Includes/Ajax/CheckoutPage.ajax.php", {p:page});
			}
			// showing last set of order items
			else if (page * pagesize >= itemcount)
			{
				$("#lblPrevPage").html('<a href="#" onclick="CheckoutAjaxPage( ' + prevPage + ', ' + pagesize + ', ' + itemcount + ')">Previous</a>');
				$("#lblPageNumber").html("Page: " + page);
				$("#lblNextPage").html("Next");
				$("#divCheckoutContainer").load("../Includes/Ajax/CheckoutPage.ajax.php", {p:page});
			}
			// showing a page between the first and last.
			else
			{
				$("#lblPrevPage").html('<a href="#" onclick="CheckoutAjaxPage( ' + prevPage + ', ' + pagesize + ', ' + itemcount + ')">Previous</a>');
				$("#lblPageNumber").html("Page: " + page);
				$("#lblNextPage").html('<a href="#" onclick="CheckoutAjaxPage( ' + nextPage + ', ' + pagesize + ', ' + itemcount + ')">Next</a>');
				$("#divCheckoutContainer").load("../Includes/Ajax/CheckoutPage.ajax.php", {p:page});
			}
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
                        <div class="col-xs-12 col-sm-3 col-md-3">
                        	<label id="lblPrevPage"></label>
                        </div>
                        <div class="col-xs-12 col-sm-2 col-md-2">
                        	<label id="lblPageNumber"></label>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3">
                        	<label id="lblNextPage"></label>
                        </div>
                        <div class="col-xs-0 col-sm-2 col-md-2">
                        </div>
                    </div>
                    
                </div>
            </div>
            <div id="divRightSidebar" class="col-md-3">
            </div>
        </div>
        
        <script type="text/javascript">
			CheckoutAjaxPage(1, <?php echo $page_size ?>, <?php echo $cart_count ?> );
		</script>

    </div>

<?php include_once("../Includes/footer.php"); ?>
</body>
</html>

