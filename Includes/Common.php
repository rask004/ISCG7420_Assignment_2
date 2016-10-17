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
	
	public static $SessionCartArrayKey = "ShoppingCart";
	
	private static $_CryptoSaltLength = 16;
	
	private static $_CryptoPassMaxLength = 48;
	private static $_CryptoPassRandomLetters = 'abcdefghijklmnopqrstuvwxyzQAZXSWEDCVFRTGBNHYUJMKILOP1234567890!@#^&_+,.' ;
	
	
	public static function getRandomSalt()
	{
		return bin2hex(random_bytes(self::$_CryptoSaltLength));
	}
	
	public static function getRandomPassword()
	{
		$pass = '';
		$max = strlen(self::$_CryptoPassRandomLetters) - 1;
		for($i = 0; $i < self::$_CryptoPassMaxLength; $i++)
		{
			$pass .= self::$_CryptoPassRandomLetters[rand(0, $max)];
		}
		
		return $pass;
	}
	
	public static function generatePasswordHash($password, $salt)
	{
		$hash = '';
		
		return $hash;
		
	}
	
}

