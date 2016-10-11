<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 10:49 PM
 */

// check if customer is logged in. if so, alter navbarLink hrefs and text to meet customer navbar requirements.
// if not, hide the checkout option to meet anonymous visitor requirements.



?>

<a id="navbarProfileLink" style="color: white; text-align: center" href="/Pages/profile.php">Register</a>
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header col-sm-4 col-md-3">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-left" href="../Pages/home.php"><img Height="80" alt="Logo" src="../images/Logo.png"/></a>
        </div>
        <div class="navbar-collapse collapse col-sm-8 col-md-9">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <ul class="nav navbar-nav navbar-left">
                            <li><a style="color: white; text-align: center" href="../Pages/contact.php">Contact Us</a></li>
                            <li><a id="navbarProfileLink" style="color: white; text-align: center" href="../Pages/profile.php">Register</a></li>
                            <li><a id="navbarLoginLink" style="color: white; text-align: center" href="../Pages/login.php">Login</a></li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <li><a id="navbarCartClearLink" style="color: white; text-align: center" href="#">Clear Cart</a></li>
                            <li><a id="navbarCheckoutLink" style="color: white; text-align: center" href="../Pages/checkout.php">Checkout</a></li>
                            <li><a id="navbarCartTotal" style="color: white; text-align: center">$ 0.00</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</nav>
