<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 12/10/2016
 * Time: 5:38 PM
 *
 *	Session specific settings
 */
 
 include_once("Common.php");

/*
 *  check if the user has been idle for too long.
 *  we cannot include SESSION because of circular imports, so
 */
session_start();

function checkTimeOut()
{
    if(isset($_SESSION[\Common\SecurityConstraints::$SessionTimestampLastVisit]))
    {
        $currentTime = time();
        $timeoutThreshold = (integer) ($_SESSION[\Common\SecurityConstraints::$SessionTimestampLastVisit])
            + \Common\SecurityConstraints::$SessionTimeOutSeconds;
        if ($currentTime > $timeoutThreshold)
        {
            $_SESSION[\Common\SecurityConstraints::$SessionCartArrayKey] = array();
            header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/logout.php");
            exit;
        }
    }
};



// if no cart is present, create one
if ( !(isset( $_SESSION[\Common\SecurityConstraints::$SessionCartArrayKey] ) 
	&& is_array($_SESSION[\Common\SecurityConstraints::$SessionCartArrayKey]) ) )
{
	$_SESSION[\Common\SecurityConstraints::$SessionCartArrayKey] = array();
};

