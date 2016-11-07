<?php

/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 17/10/2016
 * Time: 19:24 PM
 */

include_once('../Includes/Session.php');
include_once('../Includes/Common.php');

// check and log if this is an Admin
$customerId = "UNKNOWN";
$adminAccess = "FALSE";
if(isset($_SESSION[\Common\SecurityConstraints::$SessionUserIdKey]))
{
	$customerId = $_SESSION[\Common\SecurityConstraints::$SessionUserIdKey];
}
if(isset($_SESSION[\Common\SecurityConstraints::$SessionAdminCheckKey]))
{
    $adminAccess = "TRUE";
}

\Common\Logging::Log('Executing Page. sessionId=' . session_id() . '; customer='
	. $customerId . "; is_admin=" . $adminAccess."\r\n");

// redirect to home if this is not an Admin user.
if (!(isset($_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey]) && $_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey] == 1
	&& isset($_SESSION[\Common\SecurityConstraints::$SessionAdminCheckKey])))
{
    header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/logout.php");
    exit;
}
?>


<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quality Caps - Admin Section</title>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/Common.css">
    <script type="text/javascript" src="../js/jquery.min.js"></script>
    <script type="text/javascript">
        // button clicks

        // Set status to Shipped
        function ShipOrder()
        {
            var id = parseInt($("#inputItemId").val());

            $( document ).ready(function() {
                $("#divItemList").load("../Includes/Ajax/AdminOrders.ajax.php", {l:1, s:1, id:id},
                    function(responseTxt, statusTxt, xhr)
                    {
                        if(statusTxt == "success")
                        {
                            UpdateItemForm(id);
                        }
                    }
                );
            });
        };


        // load the order with this id into the form.
        function UpdateItemForm(id)
        {
            $( document ).ready(function() {
                var id_text = 'order_' + id;
                var username = '';
                var userid = $("#" + id_text).data("userid");
                if ($("#" + id_text).data("firstname") != null
                    && $("#" + id_text).data("lastname") != null)
                {
                    username = $("#" + id_text).data("firstname") + " " + $("#" + id_text).data("lastname");
                }

                var dateplaced = $("#" + id_text).data("dateplaced");
                var status = $("#" + id_text).data("status");
                var totalqty = $("#" + id_text).data("totalqty");
                var totalcaps = $("#" + id_text).data("capcount");
                var totalprice = $("#" + id_text).data("totalprice");

                $("#inputItemId").val(id);
                $("#inputItemUserId").val(userid);
                $("#inputItemUserName").val(username);
                $("#inputItemDatePlaced").val(dateplaced);
                $("#inputItemStatus").val(status);
                $("#inputItemTotalQty").val(totalqty);
                $("#inputItemTotalPrice").val(totalprice);
                $("#inputItemCapCount").val(totalcaps);
            });
        };


        // ajax calls

        // reload the list of categories.
        function UpdateItemList()
        {
            $( document ).ready(function() {
                $("#divItemList").load("../Includes/Ajax/AdminOrders.ajax.php", {l:1},
                    function(responseTxt, statusTxt, xhr)
                    {
                        if(statusTxt == "success")
                        {
                            UpdateItemForm(1);
                        }
                    }
                );
            });
        };

        // remove one category
        function DeleteItem()
        {
            var id = parseInt($("#inputItemId").val());

            $( document ).ready(function() {
                $("#divItemList").load("../Includes/Ajax/AdminOrders.ajax.php", {d:1, id:id, l:1},
                    function(responseTxt, statusTxt, xhr)
                    {
                        if(statusTxt == "success")
                        {
                            // check that the category was actually deleted. element should not exist
                            var id_text = 'order_' + id;

                            var element = document.getElementById(id_text);

                            if (typeof(element) != 'undefined' && element != null)
                            {
                                $("#lblMessage").html("ERROR: could not delete Order #" + id);
                            }
                            else
                            {
                                $("#lblMessage").html("SUCCESS: Order #" + id + " deleted.");
                            }

                            UpdateItemForm(1);
                        }
                    }
                );
            });
        };

	</script>
</head>

<body>
    <?php include_once("../Includes/navbar.admin.php"); ?>

    <div class="container-fluid PageContainer" style="overflow-y:scroll; ">

        <div class="row">
            <div id="divLeftSidebar" class="col-xs-12 col-sm-4 col-md-4">
                <div class="container-fluid PageSection" style="overflow-y:scroll; overflow-x:hidden" id="divItemList">

                </div>
            </div>
            <div id="divCentreSpace" class="col-xs-12 col-sm-8 col-md-8">

                <div class="container-fluid PageSection" >
                    <div class="row">
                        <div class="col-xs-0 col-sm-4 col-sm-4">
                        </div>
                        <div class="col-xs-12 col-sm-4 col-sm-4">
                            <H3>Categories</H3>
                        </div>
                    </div>
                    <br/>
                    <br/>
                    <div class="row">
                        <div class="col-xs-0 col-sm-1 col-sm-1">
                        </div>
                        <div class="col-xs-8 col-sm-3 col-sm-3">
                            <label for="inputItemId">ID :</label>
                        </div>
                        <div class="col-xs-6 col-sm-5 col-sm-5">
                            <input type="number" id="inputItemId" disabled />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-0 col-sm-1 col-sm-1">
                        </div>
                        <div class="col-xs-8 col-sm-3 col-sm-3">
                            <label for="inputItemUserId">UserId :</label>
                        </div>
                        <div class="col-xs-6 col-sm-5 col-sm-5">
                            <input disabled type="text" id="inputItemUserId" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-0 col-sm-1 col-sm-1">
                        </div>
                        <div class="col-xs-8 col-sm-3 col-sm-3">
                            <label for="inputItemUserName">UserName :</label>
                        </div>
                        <div class="col-xs-6 col-sm-5 col-sm-5">
                            <input disabled type="text" id="inputItemUserName" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-0 col-sm-1 col-sm-1">
                        </div>
                        <div class="col-xs-8 col-sm-3 col-sm-3">
                            <label for="inputItemDatePlaced">Date Placed :</label>
                        </div>
                        <div class="col-xs-6 col-sm-5 col-sm-5">
                            <input disabled type="text" id="inputItemDatePlaced" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-0 col-sm-1 col-sm-1">
                        </div>
                        <div class="col-xs-8 col-sm-3 col-sm-3">
                            <label for="inputItemStatus">Status :</label>
                        </div>
                        <div class="col-xs-6 col-sm-5 col-sm-5">
                            <input disabled type="text" id="inputItemStatus" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-0 col-sm-1 col-sm-1">
                        </div>
                        <div class="col-xs-8 col-sm-3 col-sm-3">
                            <label for="inputItemCapCount"># of Caps :</label>
                        </div>
                        <div class="col-xs-6 col-sm-5 col-sm-5">
                            <input disabled type="text" id="inputItemCapCount" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-0 col-sm-1 col-sm-1">
                        </div>
                        <div class="col-xs-8 col-sm-3 col-sm-3">
                            <label for="inputItemTotalQty">Total Quantity :</label>
                        </div>
                        <div class="col-xs-6 col-sm-5 col-sm-5">
                            <input disabled type="text" id="inputItemTotalQty" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-0 col-sm-1 col-sm-1">
                        </div>
                        <div class="col-xs-8 col-sm-3 col-sm-3">
                            <label for="inputItemTotalPrice">Total Price ($):</label>
                        </div>
                        <div class="col-xs-6 col-sm-5 col-sm-5">
                            <input disabled type="text" id="inputItemTotalPrice" />
                        </div>
                    </div>

                    <br/>

                    <div class="row">
                        <div class="col-xs-0 col-sm-3 col-sm-3">
                        </div>
                        <div class="col-xs-6 col-sm-2 col-sm-2">
                            <input type="button" value="Delete" id="btnDelete" onclick="DeleteItem()"/>
                        </div>
                        <div class="col-xs-6 col-sm-2 col-sm-2">

                        </div>
                        <div class="col-xs-6 col-sm-2 col-sm-2">
                            <input type="button" value="Ship Order" id="btnShip" onclick="ShipOrder()" />
                        </div>
                    </div>

                    <br/>

                    <div class="row">
                        <div class="col-xs-0 col-sm-2 col-sm-2">
                        </div>
                        <div class="col-xs-12 col-sm-8 col-sm-8" style="background-color:#979797; text-align:center">
                            <label style="font-size:2em" id="lblMessage">READY</label>
                        </div>
                        <div class="col-xs-0 col-sm-2 col-sm-2">
                        </div>
                    </div>

                    <br/>

                </div>

            </div>
        </div>
    </div>
    <br/>
    <br/>

    <label id="testing"></label>

    <script type="text/javascript">
        UpdateItemList();
    </script>

    <?php include_once("../Includes/footer.php"); ?>
</body>
</html>