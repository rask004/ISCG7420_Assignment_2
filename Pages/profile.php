<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 10:13 PM
 */

include_once('../Includes/Session.php');

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quality Caps - ???</title>
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.structure.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.theme.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/Common.css">
    <script type="text/javascript" src="../js/jquery.js"></script>
</head>

<body>
    <form method="post" enctype="multipart/form-data" autocomplete="off">


    </form>

<?= 'This is the registration / profile / edit profile page' ?>
</body>
</html>



<?php
    // check if session contains IsAuthenticated and expected value matches.
    if (isset($_SESSION['IsAuthenticated']) && $_SESSION['IsAuthenticated'] == 1)
    {
        // logged in user - change links in nav-bar.

        // logged in user - load data into form, change button behaviour.
    }
?>