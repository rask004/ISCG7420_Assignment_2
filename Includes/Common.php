<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 11:21 PM
 */

namespace Common;

// Commonly shared constants and values.

/*
	Security constraints and shared functions.
*/
class SecurityConstraints
{
	public static $SessionUserLoginKey = "UserLogin";
	public static $SessionUserIdKey = "UserId";
	public static $SessionAuthenticationKey = "IsAuthenticated";
	public static $SessionAdminCheckKey = "IsAdmin";
	
	public static $SessionCartArrayKey = "ShoppingCart";

    public static $SessionTimestampLastVisit = "TimeOfLastVisit";

    // fixed timeout in seconds

    public static $SessionTimeOutSeconds = 1800;    // 30 minutes of inactivity.

        // maximum size of salts.
	private static $_cryptoSaltLength = 16;
	
	// maximum size of hashes
	private static $_cryptoPassMaxLength = 48;
	private static $_cryptoRandomAlphaNumeric = 'abcdefghijklmnopqrstuvwxyzQAZXSWEDCVFRTGBNHYUJMKILOP1234567890!@#^&_+,.' ;
	
	/*
		generate a random salt.
	*/
	public static function getRandomSalt()
	{
		$salt = '';
		$max = strlen(self::$_cryptoRandomAlphaNumeric) - 1;
		for($i = 0; $i < self::$_cryptoSaltLength; $i++)
		{
			$salt .= self::$_cryptoRandomAlphaNumeric[rand(0, $max)];
		}
		
		return $salt;
	}
	
	/*
		create a randomised, hidden password.
	*/
	public static function getRandomPassword()
	{
		$pass = '';
		$max = strlen(self::$_cryptoRandomAlphaNumeric) - 1;
		for($i = 0; $i < self::$_cryptoPassMaxLength; $i++)
		{
			$pass .= self::$_cryptoRandomAlphaNumeric[rand(0, $max)];
		}
		
		return $pass;
	}
	
	/*
		generate a hash using the HMAC approach, with a salt and an SHA algorithm.
	*/
	public static function generatePasswordHash($password, $salt)
    {
        $hash = hash_hmac('sha256', $password, $salt);
        return $hash;
    }
}

/*
	Common shared constraints.
*/
class Constants
{

	// submission keywords for the register/profile page.
	public static $RegistrationSubmitKeyword = "Register";
	public static $ProfileUpdateKeyword = "ProfileUpdate";
	
	// Validation Regex patterns.
	public static $ValidationCharsGenericNameRegex = "/^[a-zA-Z',.\s]*/";
	
	public static $ValidationCharsLoginRegex = "/^[a-zA-Z0-9_]*/";
	
	public static $ValidationLandlineRegex = "/^0[0-9]{7,9}/";
	
	public static $ValidationCellPhoneRegex = "/^0[0-9]{8,10}/";
	
	public static $ValidationAddressRegex = "/^[0-9a-zA-Z]+\s[a-zA-z\s]+/";
	
	// for calculation of cart totals.
	
	public static $GstRate = 0.15;
	
	// default emails.
	public static $EmailAdminDefault = "AskewR04@myunitec.ac.nz";
	
	// default query string keys.
	public static $QueryStringEmailErrorKey = "EmailError";
	
	public static $QueryStringEmailSuccessKey = "EmailSuccess";
	
	// static messages
	public static  $AdminMessageSuccessfulUpload = "Success, file uploaded";
	
	public static  $AdminMessageFailedUpload = "Error, cannot upload file";
	
	public static  $AdminMessageSuccessfulDelete = "Success, file deleted";

    // static Administration data
    public static $OrderStatusWaiting = 'waiting';

    public static $OrderStatusShipped = 'shipped';

    public static $AllowedOrderStatuses = array('waiting', 'shipped');
	
	// static file upload data
	public static  $AdminFileuploadFolder = "uploaded_pictures";
	
	public static  $AdminPermittedFileuploadExtensions = array("png","jpeg","jpg");
	
	// static pagination data
	public static  $OrdersTablePageSize = 10;
	
	public static  $CheckoutTablePageSize = 3;
	
	public static  $HomeCategoriesTablePageSize = 3;
	
	public static  $HomeCapsTablePageSize = 4;
	
	public static  $HomeCapsTablePageWidth = 2;
	
	public static  $HomeCartTablePageSize = 3;
}

/*
 *  Used to manage logging.
 *
 */
class Logging
{
    private static $_loggerDirectory = "logs";

    private static $_loggerFilename = "application.log";

    /*  To log an action.
     *
     *  source:     the class or page where the logging action occurred
     *
     *  message:    the message to log.
     */
    public static function Log($message)
    {
        $logLine = date("Y/m/d H:i:s") . "%-20s, %22s, %40s, %40s, %12s, %40, %s\r\n";

        // if logging directory doesn't exist, create it.
        if ( !file_exists("../" . self::$_loggerDirectory) ||
             !file_exists("../" . self::$_loggerDirectory . "/" . self::$_loggerFilename))
        {
            mkdir("../" . self::$_loggerDirectory, 0755);
            file_put_contents("../" . self::$_loggerDirectory . "/" . self::$_loggerFilename,
                sprintf($logLine, "DATETIME", "REMOTE_ADDRESS",
                    "SCRIPT_FILENAME", "REQUESTED_URL", "METHOD",
                    "QUERY_STRING", "NOTES"),
                FILE_APPEND);
        }

        // open the file, append a log line.
        file_put_contents("../" . self::$_loggerDirectory . "/" . self::$_loggerFilename,
            sprintf($logLine, date("Y/m/d H:i:s"), $_SERVER['REMOTE_ADDR'].':'.$_SERVER['REMOTE_PORT'],
                $_SERVER['SCRIPT_FILENAME'], $_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'],
                $_SERVER['QUERY_STRING'], $message),
            FILE_APPEND);
    }
}