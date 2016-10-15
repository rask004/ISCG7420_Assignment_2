<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 15/10/2016
 * Time: 14:24 PM
 */

require_once('DataLayer.php');

namespace BusinessLayer;

// Orders business object.

class CategoryManager
{
	private $_data_manager;	
	
	function __construct()
	{
		$this->_data_manager = new \DataLayer\DataManager;
	}
	
	//TODO: add functionality for retrieving category records
}