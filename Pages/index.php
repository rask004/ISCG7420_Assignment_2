<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 10:11 PM
 */

//treat folder root as home page, using redirection

// don't allow caching of the folder root page.
header("Cache-Control: no-cache");
header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/home.php");
exit;



