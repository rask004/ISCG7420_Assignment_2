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


// Admin business object.

class AdminManager
{
	private $_dataManager;	
	
	function __construct()
	{
		$this->_dataManager = new \DataLayer\DataManager;
	}
	
	/*
		check that a supplied login matches an actual admin
	*/
	function FindMatchingLogin($login)
	{
		if ($this->_dataManager->MatchAdminByLogin($login));
		{
			return true;
		}
		
		return false;
	}
	
	/*
		retrieve a admin using their id.
		can return an empty array if admin does not exist.
	*/
	function FindAdmin($id)
	{
		return $this->_dataManager->SelectSingleAdmin($id);
	}
	
	/*
		retrieve a admin using their login.
		can return an empty array if admin does not exist.
	*/
	function FindAdminByLogin($login)
	{
		return $this->_dataManager->SelectSingleAdminByLogin($login);
	}
	
	/*
		check that a supplied login and password matches an actual admin
	*/
	function CheckMatchingPasswordForAdminLogin($login, $password)
	{
		// there is no match if there is no admin.
		if (!$this->FindMatchingLogin($login))
		{
			return false;
		}
		
		$data = $this->_dataManager->RequestAdminPasswordSaltAndHash($login);
		if (empty($data) || !isset($data['passwordsalt']) || !isset($data['passwordhash']))
		{
			return false;	
		}
		
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