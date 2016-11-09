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

// record time of last visit.
if(!isset($_SESSION[\Common\SecurityConstraints::$SessionTimestampLastVisit]))
{
    $_SESSION[\Common\SecurityConstraints::$SessionTimestampLastVisit] = time();
}

$lastVisitTime = (integer) ($_SESSION[\Common\SecurityConstraints::$SessionTimestampLastVisit]);
$timeoutThreshold = $lastVisitTime + \Common\SecurityConstraints::$SessionTimeOutSeconds;

// identify timeout and respond.
if (time() > $timeoutThreshold)
{
    // logout timedout user,
    unset($_SESSION[\Common\SecurityConstraints::$SessionAuthenticationKey]);
    unset($_SESSION[\Common\SecurityConstraints::$SessionUserLoginKey]);
    unset($_SESSION[\Common\SecurityConstraints::$SessionUserIdKey]);
    unset($_SESSION[\Common\SecurityConstraints::$SessionAdminCheckKey]);
    session_regenerate_id();

    // kill cart - assume user will not be back and prevent undesirable purchases.
    $_SESSION[\Common\SecurityConstraints::$SessionCartArrayKey] = array();

    // prevent circular redirection, update current time.
    $_SESSION[\Common\SecurityConstraints::$SessionTimestampLastVisit] = time();

    header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/home.php");
    exit;
}

