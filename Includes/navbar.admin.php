<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 10:49 PM
 *
 *	Navbar specific to admins
 */



?>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header col-sm-4 col-md-3">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <!-- NOTE: as admin should only be at admin pages, and no other pages, admin header logo leads to logout, not home page. -->
            <a class="navbar-left" href="../Pages/logout.php"><img Height="80" alt="Logo" src="../images/Logo.png"/></a>
        </div>
        <div class="navbar-collapse collapse col-sm-8 col-md-9">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <ul class="nav navbar-nav navbar-left">
                            <li><a id="navbarLogoutLink" style="color: white; text-align: center" href="../Pages/logout.php">Logout</a></li>
                            <li><a id="navbarLogoutLink" style="color: white; text-align: center" href="../Pages/AdminFiles.php">Files</a></li>
                            <li><a id="navbarLogoutLink" style="color: white; text-align: center" href="../Pages/AdminCategories.php">Categories</a></li>
                            <li><a id="navbarLogoutLink" style="color: white; text-align: center" href="../Pages/AdminSuppliers.php">Suppliers</a></li>
                            <li><a id="navbarLogoutLink" style="color: white; text-align: center" href="../Pages/AdminOrders.php">Orders</a></li>
                            <li><a id="navbarLogoutLink" style="color: white; text-align: center" href="../Pages/AdminUsers.php">Users</a></li>
                            <li><a id="navbarLogoutLink" style="color: white; text-align: center" href="../Pages/AdminCaps.php">Caps</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
