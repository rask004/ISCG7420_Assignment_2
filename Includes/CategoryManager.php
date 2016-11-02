<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 15/10/2016
 * Time: 14:24 PM
 */

namespace BusinessLayer;

require_once('DataLayer.php');

// Orders business object.

class CategoryManager
{
	private $_dataManager;	
	
	function __construct()
	{
		$this->_dataManager = new \DataLayer\DataManager;
	}
	
	// get categories with assigned caps, paginated, to show on home page. 
	public function RetrieveCategoriesForHomePage($firstRecordIndex, $numberOfRecords)
	{
		return $this->_dataManager->SelectAvailableCategoriesWithLimit($firstRecordIndex, $numberOfRecords);
	}
	
	// get count of all categories with assigned caps, paginated, to show on home page. 
	public function RetrieveCountOfCategoriesForHomePage()
	{
		return $this->_dataManager->SelectCountOfAvailableCategories();
	}
}