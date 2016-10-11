<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 10:50 PM
 */

?>

<!-- Footer -->
<div class="navbar-fixed-bottom navbar-inverse">
    <div class="col-xs-12 col-sm-4 col-md-4" style="color: white">
            <span id="footerCurrentTime" style="float:left">
            <!-- Call javascript function -->
                <script type="text/javascript">
                    function update_time() {
                        var c = new Date(); var h = c.getHours(); var m = c.getMinutes();
                        var s = c.getSeconds(); if (h < 10) { h = "0" + h; } if (m < 10) { m = "0" + m; }
                        if (s < 10) { s = "0" + s; }
                        document.getElementById("footerCurrentTime").innerHTML = h + ":" + m + ":" + s;
                        setTimeout(update_time, 1000);
                    }

                    update_time();
                </script>
            </span>
    </div>

    <div class="col-xs-12 col-sm-4 col-md-4" style="color: white">
        <span id="loginGreeting"></span>
    </div>

    <div class="col-xs-12 col-sm-4 col-md-4" style="color: white"><span style="float:right">&copy;Quality Caps LTD.</span></div>
</div>
