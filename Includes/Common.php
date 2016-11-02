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
	
	// maximum size of salts.
	private static $_CryptoSaltLength = 16;
	
	// maximum size of hashes
	private static $_CryptoPassMaxLength = 48;
	private static $_CryptoRandomAlphaNumeric = 'abcdefghijklmnopqrstuvwxyzQAZXSWEDCVFRTGBNHYUJMKILOP1234567890!@#^&_+,.' ;
	
	/*
		generate a random salt.
	*/
	public static function getRandomSalt()
	{
		$salt = '';
		$max = strlen(self::$_CryptoRandomAlphaNumeric) - 1;
		for($i = 0; $i < self::$_CryptoSaltLength; $i++)
		{
			$salt .= self::$_CryptoRandomAlphaNumeric[rand(0, $max)];
		}
		
		return $salt;
	}
	
	/*
		create a randomised, hidden password.
	*/
	public static function getRandomPassword()
	{
		$pass = '';
		$max = strlen(self::$_CryptoRandomAlphaNumeric) - 1;
		for($i = 0; $i < self::$_CryptoPassMaxLength; $i++)
		{
			$pass .= self::$_CryptoRandomAlphaNumeric[rand(0, $max)];
		}
		
		return $pass;
	}
	
	/*
		generate a hash using the HMAC approach, with a salt and an SHA algorithm.
	*/
	public static function generatePasswordHash($password, $salt)
	{
		$hash = hash_hmac( 'sha256', $password, $salt);
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
	public static $ValidationCharsGenericNameRegex = "/^[a-zA-Z',.]*/";
	
	public static $ValidationCharsLoginRegex = "/^[a-zA-Z0-9_]*/";
	
	public static $ValidationLandlineRegex = "/^0[1-9][-\s]?[1-9]{1}[0-9]{2,3}[-\s]?[0-9]{3,4}/";
	
	public static $ValidationCellPhoneRegex = "/^0[1-9]{2}[-\s]?[0-9]{3,4}[-\s]?[0-9]{3,4}/";
	
	public static $ValidationStreetAddressRegex = "#^(?:[0-9]+/)?[0-9]+[A-Za-z]?\s[A-Za-z']+(?:\s[A-Za-z']+)+#";
	
	//default emails.
	public static $EmailAdminDefault = "AskewR04@myunitec.ac.nz";
	
	// default query string keys.
	public static $QueryStringEmailErrorKey = "EmailError";
	
	// static messages
	public static  $AdminMessageSuccessfulUpload = "Success, file uploaded";
	
	public static  $AdminMessageFailedUpload = "Error, cannot upload file";
	
	public static  $AdminMessageSuccessfulDelete = "Success, file deleted";
	
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