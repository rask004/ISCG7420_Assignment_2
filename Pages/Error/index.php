<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 9:40 PM
 */

include_once('../../Includes/Common.php');

\Common\Logging::Log('Error Pages', 'Page /Pages/Error/Index.php accessed.');

// prevent visitors accessing this folder index.
header("Cache-Control: no-cache");
header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/home.php");
exit;


