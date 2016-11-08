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

        // prepare form for adding a supplier.
        function NewSupplier()
        {
            if ($("#btnAdd").val() == "New...")
            {
                // set the form for adding new item, do not allow delete. Allow undo and Save.
                $( document ).ready(function() {
                    $("#inputItemId").val("");
                    $("#inputItemName").val("");
                    $("#inputItemName").prop("disabled", false);
                    $("#inputItemEmail").val("");
                    $("#inputItemEmail").prop("disabled", false);
                    $("#inputItemHomeNum").val("");
                    $("#inputItemHomeNum").prop("disabled", false);
                    $("#inputItemWorkNum").val("");
                    $("#inputItemWorkNum").prop("disabled", false);
                    $("#inputItemMobileNum").val("");
                    $("#inputItemMobileNum").prop("disabled", false);
                    $("#btnDelete").prop("disabled", true);
                    $("#btnUndo").prop("disabled", false);
                    $("#btnAdd").prop("value", "Save");
                });
            }
            else
            {
                // save the new item.
                $( document ).ready(function() {
                    var name = $("#inputItemName").val();
                    var email = $("#inputItemEmail").val();
                    var homeNum = $("#inputItemHomeNum").val();
                    var workNum = $("#inputItemWorkNum").val();
                    var mobileNum = $("#inputItemMobileNum").val();
                    AddItem(name, email, homeNum, workNum, mobileNum);
                });

                UpdateItemForm(1);
            }
        }

        // undo any changes.
        function Undo()
        {
            var id = $("#inputItemId").val();
            if (id == "") {
                id = 1;
            }
            else {
                id = parseInt(id);
            }

            UpdateItemForm(id);
        }


        // load the category with this id into the form.
        function UpdateItemForm(id)
        {
            $( document ).ready(function() {
                var id_text = 'supplier_' + id;
                var element = $("#" + id_text);
                var name = element.data("name");
                var email = element.data("email");
                var homeNum = element.data("homenumber");
                var workNum = element.data("worknumber");
                var mobileNum = element.data("mobilenumber");

                $("#inputItemId").val(id);
                $("#inputItemName").val(name);
                $("#inputItemName").prop("disabled", true);
                $("#inputItemEmail").val(email);
                $("#inputItemEmail").prop("disabled", true);
                $("#inputItemHomeNum").val(homeNum);
                $("#inputItemHomeNum").prop("disabled", true);
                $("#inputItemWorkNum").val(workNum);
                $("#inputItemWorkNum").prop("disabled", true);
                $("#inputItemMobileNum").val(mobileNum);
                $("#inputItemMobileNum").prop("disabled", true);

                $("#btnAdd").prop("value", "New...");
                $("#btnAdd").prop("disabled", false);

                $("#btnUndo").prop("disabled", true);

                $("#btnDelete").prop("disabled", false);
            });
        };


        // ajax calls

        // reload the list of categories.
        function UpdateItemList()
        {
            $( document ).ready(function() {
                $("#divItemList").load("../Includes/Ajax/AdminSuppliers.ajax.php", {l:1},
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
                $("#divItemList").load("../Includes/Ajax/AdminSuppliers.ajax.php", {d:1, id:id, l:1},
                    function(responseTxt, statusTxt, xhr)
                    {
                        if(statusTxt == "success")
                        {
                            // check that the category was actually deleted. element should not exist
                            var id_text = 'supplier_' + id;

                            var element = document.getElementById(id_text);

                            if (typeof(element) != 'undefined' && element != null)
                            {
                                $("#lblMessage").html("ERROR: could not delete Supplier #" + id);
                            }
                            else
                            {
                                $("#lblMessage").html("SUCCESS: Supplier #" + id + " deleted.");
                            }

                            UpdateItemForm(1);
                        }
                    }
                );
            });
        };

        // add one category
        function AddItem(name, email, homeNumber, workNumber, mobileNumber)
        {
            $( document ).ready(function() {
                $("#divItemList").load("../Includes/Ajax/AdminSuppliers.ajax.php",
                    {a:1, name:name, email:email, homeNumber:homeNumber, workNumber:workNumber,
                        mobileNumber:mobileNumber, l:1},
                    function(responseTxt, statusTxt, xhr)
                    {
                        if(statusTxt == "success")
                        {
                            $("#lblMessage").html("DONE: Check list for new supplier.");

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

    <div class="container-fluid AdminContainer">

        <div class="row">
            <div id="divLeftSidebar" class="col-xs-12 col-sm-4 col-md-4">
                <div class="container-fluid PageSection" style="overflow-y:hidden; overflow-x:hidden" id="divItemList">

                </div>
            </div>
            <div id="divCentreSpace" class="col-xs-12 col-sm-8 col-md-8">

                <div class="container-fluid PageSection" >
                    <div class="row">
                        <div class="col-xs-0 col-sm-4 col-sm-4">
                        </div>
                        <div class="col-xs-12 col-sm-4 col-sm-4">
                            <H3>Suppliers</H3>
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
                            <label for="inputItemName">Name :</label>
                        </div>
                        <div class="col-xs-6 col-sm-5 col-sm-5">
                            <input disabled type="text" id="inputItemName" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-0 col-sm-1 col-sm-1">
                        </div>
                        <div class="col-xs-8 col-sm-3 col-sm-3">
                            <label for="inputItemEmail">Email :</label>
                        </div>
                        <div class="col-xs-6 col-sm-5 col-sm-5">
                            <input disabled type="text" id="inputItemEmail" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-0 col-sm-1 col-sm-1">
                        </div>
                        <div class="col-xs-8 col-sm-3 col-sm-3">
                            <label for="inputItemHomeNum">Home #:</label>
                        </div>
                        <div class="col-xs-6 col-sm-5 col-sm-5">
                            <input disabled type="text" id="inputItemHomeNum" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-0 col-sm-1 col-sm-1">
                        </div>
                        <div class="col-xs-8 col-sm-3 col-sm-3">
                            <label for="inputItemWorkNum">Work #:</label>
                        </div>
                        <div class="col-xs-6 col-sm-5 col-sm-5">
                            <input disabled type="text" id="inputItemWorkNum" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-0 col-sm-1 col-sm-1">
                        </div>
                        <div class="col-xs-8 col-sm-3 col-sm-3">
                            <label for="inputItemMobileNum">Mobile #:</label>
                        </div>
                        <div class="col-xs-6 col-sm-5 col-sm-5">
                            <input disabled type="text" id="inputItemMobileNum" />
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
                            <input type="button" value="Undo" id="btnUndo" disabled onclick="Undo()" />
                        </div>
                        <div class="col-xs-6 col-sm-2 col-sm-2">
                            <input type="button" value="New..." id="btnAdd" onclick="NewSupplier()" />
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