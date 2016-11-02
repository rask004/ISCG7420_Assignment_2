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
	private $_data_manager;	
	
	function __construct()
	{
		$this->_data_manager = new \DataLayer\DataManager;
	}
	
	/*
		Register a new customer.
	*/
	function RegisterCustomer($first_name, $last_name, $login, $password, $email, $home_number, 
									$work_number, $mobile_number, $street_address, $suburb, $city)
	{
		if ($this->findMatchingLogin($login) || $this->findMatchingEmail($email))
		{
			return false;	
		}
		
		$salt = \Common\SecurityConstraints::getRandomSalt();
		while ($this->_data_manager->MatchesUsedSalt($salt))
		{
			$salt = \Common\SecurityConstraints::getRandomSalt();
		}
		
		$hash = \Common\SecurityConstraints::generatePasswordHash($password, $salt);
		
		$this->_data_manager->InsertCustomer($first_name, $last_name, $login, $salt, $hash, $email, $home_number, $work_number,
										$mobile_number, $street_address, $suburb, $city);
										
		return true;
	}
	
	/*
		Update the profile of a customer.
		The customer must exist.
		The password hash and salt are NOT updated here. See "UpdatePassword(...)" for password changes.
	*/
	function UpdateProfile($first_name, $last_name, $login, $email, $home_number, 
									$work_number, $mobile_number, $street_address, $suburb, $city, $id)
	{
		if ($this->findMatchingLogin($login) || $this->findMatchingEmail($email))
		{
			return false;	
		}
		
		$this->_data_manager->UpdateCustomerButNotPassword($first_name, $last_name, $login, $email, $home_number, 
									$work_number, $mobile_number, $street_address, $suburb, $city, $id);
		
		return true;
	}
	
	/*
		given a customer id, update the password with a new hash and randomised salt.
	*/
	function UpdatePassword($password, $id)
	{
		$salt = \Common\SecurityConstraints::getRandomSalt();
		while ($this->_data_manager->MatchesUsedSalt($salt))
		{
			$salt = \Common\SecurityConstraints::getRandomSalt();
		}
		
		$hash = \Common\SecurityConstraints::generatePasswordHash($password, $salt);
		
		if (!$this->_data_manager->UpdateCustomerPasswordOnly($salt, $hash, $id))
		{
			return false;	
		}
		
		return true;
	}
	
	/*
		check that a supplied email matches an actual customer
	*/
	function findMatchingEmail($email)
	{
		if ($this->_data_manager->MatchCustomerByEmail($email))
		{
			return true;
		}
		
		return false;
	}
	
	/*
		check that a supplied login matches an actual customer
	*/
	function findMatchingLogin($login)
	{
		if ($this->_data_manager->MatchCustomerByLogin($login))
		{
			return true;
		}
		
		return false;
	}
	
	/*
		retrieve a customer using their id.
		can return an empty array if customer does not exist.
	*/
	function findCustomer($id)
	{
		return $this->_data_manager->SelectSingleCustomer($id);
	}
	
	/*
		retrieve a customer using their login.
		can return an empty array if customer does not exist.
	*/
	function findCustomerByLogin($login)
	{
		return $this->_data_manager->SelectSingleCustomerByLogin($login);
	}
	
	/*
		check that a supplied login and password matches an actual customer
	*/
	function checkMatchingPasswordForCustomerLogin($login, $password)
	{
		// there is no match if there is no customer.
		if (!$this->findMatchingLogin($login))
		{
			return false;
		}
		
		$data = $this->_data_manager->RequestCustomerPasswordSaltAndHash($login);
		$salt = $data['passwordsalt'];
		$expected_hash = $data['passwordhash'];
		
		$comparison_hash = \Common\SecurityConstraints::generatePasswordHash($password, $salt);
		
		if ($comparison_hash === $expected_hash)
		{
			return true;	
		}
		
		return false;
	}	
	
}