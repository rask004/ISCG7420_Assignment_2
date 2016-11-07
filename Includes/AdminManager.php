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

	/*
	 *  get all users
	 */
	public function GetAllUsers()
    {
        return $this->_dataManager->SelectAllSiteUsers();
    }

    /*
	 *  get all categories
	 */
    public function GetAllCategories()
    {
        return $this->_dataManager->SelectAllCategories();
    }

    /*
	 *  get all caps
	 */
    public function GetAllCaps()
    {
        return $this->_dataManager->SelectAllCaps();
    }

    /*
	 *  get all suppliers
	 */
    public function GetAllSuppliers()
    {
        return $this->_dataManager->SelectAllSuppliers();
    }

    /*
	 *  get all orders
	 */
    public function GetAllOrders()
    {
        return $this->_dataManager->SelectAllOrders();
    }

    /*
	 *  delete order
	 */
    public function DeleteOrder($id)
    {
        $this->_dataManager->DeleteOrder($id);
    }

    /*
	 *  delete cap
	 */
    public function DeleteCap($id)
    {
        $this->_dataManager->DeleteCap($id);
    }

    /*
	 *  delete user
	 */
    public function DeleteUser($id)
    {
        $this->_dataManager->DeleteUser($id);
    }

    /*
	 *  delete supplier
	 */
    public function DeleteSupplier($id)
    {
        $this->_dataManager->DeleteSupplier($id);
    }

    /*
	 *  delete category
	 */
    public function DeleteCategory($id)
    {
        $this->_dataManager->DeleteCategory($id);
    }

    /*
	 *  add Category
	 */
    public function AddCategory($name)
    {
        $this->_dataManager->InsertCategory($name);
    }

    /*
	 *  add Supplier
	 */
    public function AddSupplier($name, $email, $homeNumber, $workNumber, $mobileNumber)
    {
        $this->_dataManager->InsertSupplier($name, $email, $homeNumber, $workNumber, $mobileNumber);
    }

    /*
	 *  add Cap
	 */
    public function AddCap($name, $price, $description, $imageUrl, $supplierId, $categoryId)
    {
        $this->_dataManager->InsertCap($name, $price, $description, $imageUrl, $supplierId, $categoryId);
    }

    /*
	 *  set a cap as obsolete (remove from home page)
	 */
    public function RetireCap($id)
    {
        $this->_dataManager->RetireCap($id);
    }

    /*
	 *  set a cap as obsolete (remove from home page)
	 */
    public function ChangeOrderStatus($id, $status)
    {
        $this->_dataManager->ChangeOrderStatus($id, $status);
    }

    /*
	 *  disable a customer
	 */
    public function DisableCustomer($id)
    {
        $this->_dataManager->DisableCustomer($id);
    }
}