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

// Orders management business object.
class OrderManager
{
	private $_data_manager;	
	
	function __construct()
	{
		$this->_data_manager = new \DataLayer\DataManager;
	}
	
	//TODO: add functionality for managing order (item) records
	
	/* 
		generate a new order.
	*/
	function PlaceOrder($customer_id, array $caps_with_quantities)
	{
		$this->_data_manager->insertOrder($customer_id, $caps_with_quantities);
	}
	
	/* 
		retrieve all orders for a customer
	*/
	function GetAllOrdersForCustomer($customer_id, $start_index, $number_of_items)
	{
		return $this->_data_manager->selectOrdersWithItemsByCustomer($customer_id, $start_index, $number_of_items);
	}
	
	/* 
		retrieve all order summaries for a customer
	*/
	function GetAllOrderSummariesForCustomer($customer_id, $start_index, $number_of_items)
	{
		return $this->_data_manager->selectOrderSummariesByCustomer($customer_id, $start_index, $number_of_items);
	}
	
	/* 
		retrieve count of order summaries for a customer
	*/
	function GetCountOfOrderSummariesByCustomer($customer_id)
	{
		return $this->_data_manager->GetCountOfOrderSummariesByCustomer($customer_id);
	}
	
}