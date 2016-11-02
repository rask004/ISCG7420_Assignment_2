<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 15/10/2016
 * Time: 14:24 PM
 */

namespace BusinessLayer;

require_once('DataLayer.php');

// Orders management business object.
class OrderManager
{
	private $_dataManager;	
	
	function __construct()
	{
		$this->_dataManager = new \DataLayer\DataManager;
	}
	
	/* 
		generate a new order.
	*/
	function PlaceOrder($customerId, array $capsWithQuantities)
	{
		$this->_dataManager->InsertOrder($customerId, $capsWithQuantities);
	}
	
	/* 
		retrieve all orders for a customer
	*/
	function GetAllOrdersForCustomer($customerId, $startIndex, $numberOfIitems)
	{
		return $this->_dataManager->SelectOrdersWithItemsByCustomer($customerId, $startIndex, $numberOfIitems);
	}
	
	/* 
		retrieve all order summaries for a customer
	*/
	function GetAllOrderSummariesForCustomer($customerId, $startIndex, $numberOfIitems)
	{
		return $this->_dataManager->SelectOrderSummariesByCustomer($customerId, $startIndex, $numberOfIitems);
	}
	
	/* 
		retrieve count of order summaries for a customer
	*/
	function GetCountOfOrderSummariesByCustomer($customerId)
	{
		return $this->_dataManager->GetCountOfOrderSummariesByCustomer($customerId);
	}
	
}