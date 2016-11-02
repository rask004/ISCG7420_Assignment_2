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
	private $_data_manager;	
	
	function __construct()
	{
		$this->_data_manager = new \DataLayer\DataManager;
	}
	
	/*
		Retrieve a single cap.
	*/
	function GetSingleCap($capId)
	{
		return $this->_data_manager->SelectSingleCap($capId);
	}
	
	/*
		Retrieve a page of caps.
	*/
	function GetCapsByCategorywithLimit($categoryId, $firstCapIndex, $numberOfCaps)
	{
		return $this->_data_manager->SelectCapsbyCategoryIdWithLimit($categoryId, $firstCapIndex, $numberOfCaps);
	}
	
	/*
		Retrieve all caps.
	*/
	function GetAllCaps($firstCapIndex, $numberOfCaps)
	{
		return $this->_data_manager->SelectAllCaps($firstCapIndex, $numberOfCaps);
	}
	
	/*
		Retrieve count of caps for a category.
	*/
	function GetCapsByCategoryCount($categoryId)
	{
		return $this->_data_manager->SelectCountOfCapsbyCategoryId($categoryId);
	}
	
	/*
		Retrieve count of all caps.
	*/
	function GetAllCapsCount()
	{
		return $this->_data_manager->SelectCountOfAllCaps();
	}
}