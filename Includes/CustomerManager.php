<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 15/10/2016
 * Time: 14:24 PM
 */
 
namespace BusinessLayer;

require_once('DataLayer.php');
require_once('Common.php');

// Customer management business object.
class CustomerManager
{
	private $_dataManager;	
	
	function __construct()
	{
		$this->_dataManager = new \DataLayer\DataManager;
	}
	
	/*
		Register a new customer.
	*/
	function RegisterCustomer($firstName, $lastName, $login, $password, $email, $homeNumber, 
									$workNumber, $mobileNumber, $streetAddress, $suburb, $city)
	{
		$salt = \Common\SecurityConstraints::getRandomSalt();
		while ($this->_dataManager->MatchesUsedSalt($salt))
		{
			$salt = \Common\SecurityConstraints::getRandomSalt();
		}
		
		$hash = \Common\SecurityConstraints::generatePasswordHash($password, $salt);
		
		$this->_dataManager->InsertCustomer($firstName, $lastName, $login, $salt, $hash, $email, $homeNumber, $workNumber,
										$mobileNumber, $streetAddress, $suburb, $city);
										
		return true;
	}
	
	/*
		Update the profile of a customer.
		The customer must exist.
		The password hash and salt are NOT updated here. See "UpdatePassword(...)" for password changes.
	*/
	function UpdateProfile($firstName, $lastName, $login, $email, $homeNumber, 
									$workNumber, $mobileNumber, $streetAddress, $suburb, $city, $id)
	{				
		$this->_dataManager->UpdateCustomerButNotPassword($firstName, $lastName, $login, $email, $homeNumber, 
									$workNumber, $mobileNumber, $streetAddress, $suburb, $city, $id);
		
		return true;
	}
	
	/*
		given a customer id, update the password with a new hash and randomised salt.
	*/
	function UpdatePassword($password, $id)
	{
		$salt = \Common\SecurityConstraints::getRandomSalt();
		while ($this->_dataManager->MatchesUsedSalt($salt))
		{
			$salt = \Common\SecurityConstraints::getRandomSalt();
		}
		
		$hash = \Common\SecurityConstraints::generatePasswordHash($password, $salt);
		
		if (!$this->_dataManager->UpdateCustomerPasswordOnly($salt, $hash, $id))
		{
			return false;	
		}
		
		return true;
	}
	
	/*
		check that a supplied email matches an actual customer
	*/
	function FindMatchingEmail($email)
	{
		if ($this->_dataManager->MatchCustomerByEmail($email))
		{
			return true;
		}
		
		return false;
	}
	
	/*
		check that a supplied login matches an actual customer
	*/
	function FindMatchingLogin($login)
	{
		if ($this->_dataManager->MatchCustomerByLogin($login))
		{
			return true;
		}
		
		return false;
	}
	
	/*
		retrieve a customer using their id.
		can return an empty array if customer does not exist.
	*/
	function FindCustomer($id)
	{
		return $this->_dataManager->SelectSingleCustomer($id);
	}
	
	/*
		retrieve a customer using their login.
		can return an empty array if customer does not exist.
	*/
	function FindCustomerByLogin($login)
	{
		return $this->_dataManager->SelectSingleCustomerByLogin($login);
	}
	
	/*
		check that a supplied login and password matches an actual customer
	*/
	function CheckMatchingPasswordForCustomerLogin($login, $password)
	{
		// there is no match if there is no customer.
		if (!$this->FindMatchingLogin($login))
		{
			return false;
		}
		
		$data = $this->_dataManager->RequestCustomerPasswordSaltAndHash($login);
		$salt = $data['passwordsalt'];
		$expectedHash = $data['passwordhash'];
		
		$comparisonHash = \Common\SecurityConstraints::generatePasswordHash($password, $salt);
		
		if ($comparisonHash === $expectedHash)
		{
			return true;	
		}
		
		return false;
	}	
	
}