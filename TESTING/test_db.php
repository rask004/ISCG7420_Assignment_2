<?php  
	include_once( '../Includes/DataLayer.php');  
	
	ini_set('display_errors', '1');

?>
	
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quality Caps -
        <?php
        if (isset($_SESSION['IsAuthenticated']) && $_SESSION['IsAuthenticated'] == 1)
        {
            echo 'Profile';
        }
        else
        {
            echo 'Register';
        }
        ?>
    </title>
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.structure.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.theme.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/Common.css">
    <script type="text/javascript" src="../js/jquery.js"></script>
</head>
<body>

	<?php $dataManager = new \DataLayer\DataManager; ?>

</body>
</html>