<?php

/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 17/10/2016
 * Time: 19:24 PM
 */

include_once('../Includes/Session.php');
include_once('../Includes/Common.php');

// for timeout
$_SESSION[\Common\SecurityConstraints::$SessionTimestampLastVisit] = time();


include_once('../Includes/AdminManager.php');

use \BusinessLayer\AdminManager;

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

$Manager = new AdminManager();

?>


<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quality Caps - Admin Section</title>
    <script type="text/javascript" src="../js/jquery.min.js"></script>
    <script type="text/javascript" src="../js/bootstrap.min.js"></script>
    <script type="text/javascript">
        // drop-down clicks

        function SetCategoryId(id)
        {
            $("#inputItemCategoryId").val(id);
        }

        function SetSupplierId(id)
        {
            $("#inputItemSupplierId").val(id);
        }

        function SetImageUrl(url)
        {
            $("#inputItemImage").val(url);
        }

        // button clicks

        // prepare form for adding a category.
        function NewCap()
        {
            if ($("#btnAdd").val() == "New...")
            {
                // set the form for adding new item, do not allow delete. Allow undo and Save.
                $( document ).ready(function()
                {
                    $("#inputItemId").val("");
                    $("#inputItemName").val("");
                    $("#inputItemName").prop("disabled", false);
                    $("#inputItemPrice").val("1.00");
                    $("#inputItemPrice").prop("disabled", false);
                    $("#inputItemDesc").val("");
                    $("#inputItemDesc").prop("disabled", false);
                    $("#inputItemCategoryId").val("");
                    $("#inputItemCategoryId").prop("disabled", false);
                    $("#divDropCategoryId").prop("hidden", false);
                    $("#inputItemSupplierId").val("");
                    $("#inputItemSupplierId").prop("disabled", false);
                    $("#divDropSupplierId").prop("hidden", false);
                    $("#inputItemImage").val("");
                    $("#inputItemImage").prop("disabled", false);
                    $("#divDropImageUrl").prop("hidden", false);

                    $("#btnDelete").prop("disabled", true);
                    $("#btnRetire").prop("disabled", true);
                    $("#btnUndo").prop("disabled",false);
                    $("#btnAdd").prop("value", "Save");
                });
            }
            else
            {
                // save the new item.
                $( document ).ready(function() {
                    var name = $("#inputItemName").val();
                    var price = parseFloat($("#inputItemPrice").val());
                    var description = $("#inputItemDesc").val();
                    var imageUrl = $("#inputItemImage").val();
                    var supplierId = $("#inputItemSupplierId").val();
                    var categoryId = $("#inputItemCategoryId").val();

                    if (imageUrl == "" || supplierId == "" || categoryId == "")
                    {
                        $("#lblMessage").html("ERROR: You must select a category id (or NULL), a supplier Id and an image.");
                    }
                    else if (description == "")
                    {
                        $("#lblMessage").html("ERROR: You must provide a description.");
                    }
                    else if (name == "")
                    {
                        $("#lblMessage").html("ERROR: You must provide a name.");
                    }
                    else if (price < 1.0)
                    {
                        $("#lblMessage").html("ERROR: The price is too low! Must be at least $1.00.");
                    }
                    else
                    {
                        AddItem(name, price, description, imageUrl, supplierId, categoryId);
                        UpdateItemForm(1);
                    }

                });


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
            $("#lblMessage").html("READY");
        }


        // load the category with this id into the form.
        function UpdateItemForm(id)
        {
            $( document ).ready(function() {
                var id_text = 'cap_' + id;
                var name = $("#" + id_text).data("name");
                var desc = $("#" + id_text).data("description");
                var price = $("#" + id_text).data("price");
                var imageUrl = $("#" + id_text).data("imageurl");
                var supplierId = $("#" + id_text).data("supplierid");
                var categoryId = $("#" + id_text).data("categoryid");

                if (categoryId == "")
                {
                    categoryId = "Not Assigned.";
                }

                $("#inputItemId").val(id);
                $("#inputItemName").val(name);
                $("#inputItemName").prop("disabled", true);
                $("#inputItemPrice").val(price);
                $("#inputItemPrice").prop("disabled", true);
                $("#inputItemDesc").val(desc);
                $("#inputItemDesc").prop("disabled", true);
                $("#inputItemImage").val(imageUrl);
                $("#inputItemSupplierId").val(supplierId);
                $("#inputItemCategoryId").val(categoryId);

                $("#inputItemCategoryId").prop("disabled", true);
                $("#inputItemImage").prop("disabled", true);
                $("#inputItemSupplierId").prop("disabled", true);
                $("#divDropCategoryId").prop("hidden", true);
                $("#divDropSupplierId").prop("hidden", true);
                $("#divDropImageUrl").prop("hidden", true);

                $("#btnAdd").prop("value", "New...");
                $("#btnAdd").prop("disabled", false);

                $("#btnUndo").prop("disabled", true);

                $("#btnRetire").prop("disabled", false);

                $("#btnDelete").prop("disabled", false);
            });
        };


        // ajax calls

        // reload the list of categories.
        function UpdateItemList()
        {
            $( document ).ready(function() {
                $("#divItemList").load("../Includes/Ajax/AdminCaps.ajax.php", {l:1},
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
                $("#divItemList").load("../Includes/Ajax/AdminCaps.ajax.php", {d:1, id:id, l:1},
                    function(responseTxt, statusTxt, xhr)
                    {
                        if(statusTxt == "success")
                        {
                            // check that the cap was actually deleted. element should not exist
                            var id_text = 'cap_' + id;

                            var element = document.getElementById(id_text);

                            if (typeof(element) != 'undefined' && element != null)
                            {
                                $("#lblMessage").html("ERROR: could not delete Cap #" + id);
                            }
                            else
                            {
                                $("#lblMessage").html("SUCCESS: Cap #" + id + " deleted.");
                            }

                            UpdateItemForm(1);
                        }
                    }
                );
            });
        };

        // add one category
        function AddItem(name, price, description, imageUrl, supplierId, categoryId)
        {
            $( document ).ready(function() {
                $("#divItemList").load("../Includes/Ajax/AdminCaps.ajax.php",
                    {a:1, name:name, price:price, description:description, imageUrl:imageUrl,
                        supplierId:supplierId, categoryId:categoryId, l:1},
                    function(responseTxt, statusTxt, xhr)
                    {
                        if(statusTxt == "success")
                        {
                            $("#lblMessage").html("DONE: Check list for new cap.");

                            UpdateItemForm(1);
                        }
                    }
                );
            });
        };

        // set the category to null.
        function RetireCap()
        {
            var id = parseInt($("#inputItemId").val());

            $( document ).ready(function() {
                $("#divItemList").load("../Includes/Ajax/AdminCaps.ajax.php",
                    {r:1, id:id, l:1},
                    function(responseTxt, statusTxt, xhr)
                    {
                        if(statusTxt == "success")
                        {
                            $("#lblMessage").html("DONE. Check category is empty.");

                            UpdateItemForm(id);
                        }
                    }
                );
            });
        };
	</script>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/Common.css">
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
                            <H3>Categories</H3>
                        </div>
                    </div>
                    <br/>
                    <br/>
                    <div class="row">
                        <div class="col-xs-0 col-sm-1 col-md-1">
                        </div>
                        <div class="col-xs-8 col-sm-3 col-md-3">
                            <label for="inputItemId">ID :</label>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6">
                            <input type="number" style="width:100%" id="inputItemId" disabled />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-0 col-sm-1 col-md-1">
                        </div>
                        <div class="col-xs-8 col-sm-3 col-md-3">
                            <label for="inputItemName">Name :</label>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6">
                            <input required disabled type="text" style="width:100%" id="inputItemName" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-0 col-sm-1 col-md-1">
                        </div>
                        <div class="col-xs-8 col-sm-3 col-md-3">
                            <label for="inputItemPrice">Price ($):</label>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6">
                            <input required disabled type="number" style="width:100%" step="any" min="1.00" id="inputItemPrice" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-0 col-sm-1 col-md-1">
                        </div>
                        <div class="col-xs-8 col-sm-3 col-md-3">
                            <label for="inputItemDesc">Description :</label>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6">
                            <textarea required  disabled rows="5" style="width:100%" id="inputItemDesc" ></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-0 col-sm-1 col-md-1">
                        </div>
                        <div class="col-xs-8 col-sm-3 col-md-3">
                            <label for="inputItemImage">Image Url :</label>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3">
                            <input required readonly disabled type="text" style="width:100%" id="inputItemImage" />
                        </div>
                        <div class="col-xs-8 col-sm-3 col-md-3">
                            <div hidden id="divDropImageUrl" class="dropdown">
                                <button class="dropdown-toggle" type="button"
                                        data-toggle="dropdown">Image Url
                                    <span class="caret"></span></button>
                                <ul id="ulImageUrl" class="dropdown-menu dropdown-menu-left">
                                    <?php
                                        // populate image urls dropdown with filenames. clicking an filename loads it in the image url input.
                                        // get list of files in upload folder.
                                        $contents = scandir( "../" . \Common\Constants::$AdminFileuploadFolder);
                                        foreach ($contents as $key => $value)
                                        {
                                            $file_parts = explode(".", $value);
                                            $extension = $file_parts[count($file_parts) - 1 ];
                                            // remove directory indicators, and non-permitted files.
                                            if ($value == "." || $value == ".."
                                                || !in_array($extension, \Common\Constants::$AdminPermittedFileuploadExtensions))
                                            {
                                                unset($contents[$key]);
                                            }
                                        }

                                        foreach(array_values($contents) as $url)
                                        {
                                            echo '<li><a href="#" onclick="SetImageUrl(\'' . $url . '\')">' . $url . '</a></li>';
                                        }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-0 col-sm-1 col-md-1">
                        </div>
                        <div class="col-xs-8 col-sm-3 col-md-3">
                            <label for="inputItemSupplierId">Supplier Id :</label>
                        </div>
                        <div class="col-xs-12 col-sm-3 col-md-3">
                            <input required readonly disabled type="text" style="width:100%" id="inputItemSupplierId" />
                        </div>
                        <div class="col-xs-8 col-sm-3 col-md-3">
                            <div hidden id="divDropSupplierId" class="dropdown">
                                <button class="dropdown-toggle" type="button"
                                        data-toggle="dropdown">Supplier Id
                                    <span class="caret"></span></button>
                                <ul id="ulSupplierId" class="dropdown-menu dropdown-menu-left">
                                    <?php
                                        $Suppliers = $Manager->GetAllSuppliers();

                                        // populate supplier dropdown with supplier ids. clicking an
                                        // id loads it in the supplier id input.
                                        foreach ($Suppliers as $supplier)
                                        {
                                            $id = $supplier['id'];
                                            echo '<li><a href="#" onclick="SetSupplierId(' . $id . ')">' .
                                                $supplier['name'] . '</a></li>';
                                        }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-0 col-sm-1 col-md-1">
                        </div>
                        <div class="col-xs-6 col-sm-3 col-md-3">
                            <label for="inputItemCategoryId">Category Id :</label>
                        </div>
                        <div class="col-xs-6 col-sm-3 col-md-3">
                            <input required readonly disabled type="text" style="width:100%" id="inputItemCategoryId" />
                        </div>
                        <div class="col-xs-8 col-sm-3 col-md-3">
                            <div hidden id="divDropCategoryId" class="dropdown">
                                <button class="dropdown-toggle" type="button"
                                        data-toggle="dropdown">Category Id
                                    <span class="caret"></span></button>
                                <ul id="ulCategoryId" class="dropdown-menu dropdown-menu-left">
                                    <?php
                                        $Categories = $Manager->GetAllCategories();
                                        // populate supplier dropdown with supplier ids. clicking an
                                        // id loads it in the supplier id input.
                                        foreach ($Categories as $category)
                                        {
                                            echo '<li><a href="#" onclick="SetCategoryId(' . $category['id'] . ')">' .
                                                $category['name'] . '</a></li>';
                                        }

                                        echo '<li><a href="#" onclick="SetCategoryId(-1)">None</a></li>';

                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <br/>

                    <div class="row">
                        <div class="col-xs-0 col-sm-2 col-md-2">
                        </div>
                        <div class="col-xs-6 col-sm-2 col-md-2">
                            <input type="button" value="Delete" id="btnDelete" onclick="DeleteItem()"/>
                        </div>
                        <div class="col-xs-6 col-sm-2 col-md-2">
                            <input type="button" value="Undo" id="btnUndo" disabled onclick="Undo()" />
                        </div>

                        <div class="col-xs-6 col-sm-2 col-md-2">
                            <input type="button" value="New..." id="btnAdd" onclick="NewCap()" />
                        </div>
                        <div class="col-xs-6 col-sm-2 col-md-2">
                            <input type="button" value="Remove Category" id="btnRetire" onclick="RetireCap()" />
                        </div>
                    </div>

                    <br/>

                    <div class="row">
                        <div class="col-xs-0 col-sm-2 col-md-2">
                        </div>
                        <div class="col-xs-12 col-sm-8 col-md-8" style="background-color:#979797; text-align:center">
                            <label style="font-size:2em" id="lblMessage">READY</label>
                        </div>
                        <div class="col-xs-0 col-sm-2 col-md-2">
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