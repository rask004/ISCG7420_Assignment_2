<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 11:21 PM
 */

namespace Common;

// Commonly shared constants and values.

class Security
{
	public static $SessionUserLoginKey = "UserLogin";
	public static $SessionUserIdKey = "UserId";
	public static $SessionAuthenticationKey = "IsAuthenticated";
	public static $SessionAdminCheckKey = "IsAdmin";
	
	public static $SessionCartArrayKey = "ShoppingCart";
	
	private static $_CryptoSaltLength = 16;
	
	private static $_CryptoPassMaxLength = 48;
	private static $_CryptoRandomAlphaNumeric = 'abcdefghijklmnopqrstuvwxyzQAZXSWEDCVFRTGBNHYUJMKILOP1234567890!@#^&_+,.' ;
	
	
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
	
	public static function generatePasswordHash($password, $salt)
	{
		$hash = hash_hmac( 'sha256', $password, $salt);
		return $hash;
	}
	
}

