<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 15/10/2016
 * Time: 14:24 PM
 */
 
 namespace BusinessLayer;

require_once('DataLayer.php');

// Caps business object.
class CapManager
{
	private $_dataManager;	
	
	function __construct()
	{
		$this->_dataManager = new \DataLayer\DataManager;
	}
	
	/*
		Retrieve a single cap.
	*/
	function GetSingleCap($capId)
	{
		return $this->_dataManager->SelectSingleCap($capId);
	}
	
	/*
		Retrieve a page of caps.
	*/
	function GetCapsByCategorywithLimit($categoryId, $firstCapIndex, $numberOfCaps)
	{
		return $this->_dataManager->SelectCapsbyCategoryIdWithLimit($categoryId, $firstCapIndex, $numberOfCaps);
	}
	
	/*
		Retrieve all caps.
	*/
	function GetAllCaps($firstCapIndex, $numberOfCaps)
	{
		return $this->_dataManager->SelectAllCapsWithCategoriesWithLimit($firstCapIndex, $numberOfCaps);
	}
	
	/*
		Retrieve count of caps for a category.
	*/
	function GetCapsByCategoryCount($categoryId)
	{
		return $this->_dataManager->SelectCountOfCapsbyCategoryId($categoryId);
	}
	
	/*
		Retrieve count of all caps.
	*/
	function GetAllCapsCount()
	{
		return $this->_dataManager->SelectCountOfAllCaps();
	}
}