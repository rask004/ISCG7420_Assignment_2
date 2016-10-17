<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 15/10/2016
 * Time: 14:24 PM
 */

require_once('DataLayer.php');

namespace BusinessLayer;

// Caps business object.

class CustomerManager
{
	private $_data_manager;	
	
	function __construct()
	{
		$this->_data_manager = new \DataLayer\DataManager;
	}
	
	// TODO: add functionality for managing customers.
	
	function RegisterCustomer($first_name, $last_name, $login, $password, $email, $home_number, 
									$work_number, $mobile_number, $street_address, $suburb, $city)
	{
		
	}
	
	function UpdateProfile($first_name, $last_name, $login, $email, $home_number, 
									$work_number, $mobile_number, $street_address, $suburb, $city, $id)
	{
		
	}
	
	function findMatchingEmail($email)
	{
		
	}
	
	function findMatchingLogin($login)
	{
		
	}
	
	function findCustomer($id)
	{
		
	}
	
	function findCustomerByLogin($login)
	{
		
	}
	
	
}