<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 11:24 PM
 */

namespace DataLayer;

// Database interaction object.

class DataManager
{
	private $_conn;
	
	private function _buildTables()
	{
		$this->_openConnection();
		
		if(!$this->_conn->query("create table if not exists `SiteUser`(`id` int UNSIGNED AUTO_INCREMENT primary key, `login`   varchar(64)    not null, " .
                            "`passwordhash`    varchar(64)    not null, `userType`    char(1)     not null, `emailAddress`    varchar(100)   not null, " .
                            "`homeNumber`  varchar(11), `workNumber`  varchar(11), `mobileNumber`    varchar(14), `firstName`   varchar(32), " .
                            "`lastName`    varchar(32), `streetAddress`   varchar(64), `suburb`      varchar(24), `city`        varchar(16), " .
                            "`isDisabled`  BIT(1)    not null DEFAULT 0 );"))
		{
			echo ($this->_conn->errno . "; " . $this->_conn->error);
		}
		

        if(!$this->_conn->query("create table if not exists CustomerOrder(`id` int UNSIGNED AUTO_INCREMENT primary key, `userId` int UNSIGNED not null, " .
                           "`status` varchar(7) not null DEFAULT 'waiting', `datePlaced` datetime not null, ".
						   "CONSTRAINT fk_OrderCustomer Foreign Key (`userId`) References `SiteUser`(`id`));"))
		{
			echo ($this->_conn->errno . "; " . $this->_conn->error);
		}
		

        if(!$this->_conn->query("create table if not exists `Supplier`(`id` int UNSIGNED AUTO_INCREMENT primary key, `name` varchar(32) not null, " .
                            "`homeNumber`   varchar(11) null, `worknumber` varchar(11) null, " .
                            "`mobileNumber` varchar(13) null, `emailAddress` varchar(64) not null);"))
		{
			echo ($this->_conn->errno . "; " . $this->_conn->error);
		}
		

        if(!$this->_conn->query("create table if not exists `Category`(`id` int UNSIGNED AUTO_INCREMENT primary key, `name` varchar(40) not null); "))
		{
			echo ($this->_conn->errno . "; " . $this->_conn->error);
		}
		

        if(!$this->_conn->query("create table if not exists `Cap`(`id` int UNSIGNED AUTO_INCREMENT primary key, `name`    varchar(40)    not null, " .
                            "`price` real UNSIGNED not null, `description` varchar(512)   not null, `imageUrl` varchar(96) not null, " .
                            "`supplierId` int UNSIGNED not null,    " .
                            "`categoryId` int UNSIGNED not null,    " . 
							"CONSTRAINT fk_supplier Foreign Key (`supplierId`) References  `Supplier`(`id`), " .
							"CONSTRAINT fk_category Foreign Key (`categoryId`) References `Category`(`id`)); "))
		{
			echo ($this->_conn->errno . "; " . $this->_conn->error);
		}
		

        if(!$this->_conn->query("create table if not exists `OrderItem`(`orderId` int UNSIGNED not null , " .
                                                       "`capId` int UNSIGNED not null, " .
                                                       "`quantity` smallint UNSIGNED not null, " .
                                                       "Constraint orderItem_pk Primary Key(`capId`, `orderId`), ".
													   "CONSTRAINT fk_OrderOrderItem Foreign Key (`orderId`) References `CustomerOrder`(`id`), ". 
													   "CONSTRAINT fk_capOrderItem Foreign Key (`capId`) References `Cap`(`id`)); "))
		{
			echo ($this->_conn->errno . "; " . $this->_conn->error);
		}
														  
		$this->_closeConnection();
	}
	
	private function _openConnection()
	{
		$this->_conn = new \mysqli("localhost", "askewr04", "29101978", "askewr04mysql3");
		$this->_conn->set_charset('utf8');	
	}
	
	private function _closeConnection()
	{
		$this->_conn->close();
	}
	
	function __construct()
	{
		$this->_buildTables();
	}
	
	
	/*
		given a customer id and a list of caps with quantities, insert a new order.
	*/
	public function insertOrder( $customer_id, array $cap_quantity_list) 
	{		
		// there must be caps to generate orderitems from. if not, do nothing.
		if ( count( $cap_quantity_list) > 0 ) 
		{		
			$this->_openConnection();
			
			$id = (integer) $id;
			
			// need to know the orderid. request a new order id first.
			$result = $this->_conn->query("SHOW TABLE STATUS LIKE 'CustomerOrder'");
			$data = $this->_conn->fetch_assoc($result);
			$next_order_id = $data['auto_increment'];
			
			$now = new DateTime();
			
			$this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
			// create the customer order.
			$sql = "insert into CustomerOrder (id, userId, datePlaced) values (".$next_order_id.",".$customer_id.",".$now->format('Y-m-d H:i:s').");";
			$this->_conn->query($sql);

			// create each new order item, using the order id.
			$sql = "insert into OrderItem (orderId, capId, quantity ) values ";			
			$first_item = true;
			foreach ($cap_quantity_list as $cap_item)
			{
				if ($first_item) 
				{
					$sql .= "(";
					$first_item = false;
				}
				else 
				{
					$sql .= ",(";
				}
					
				$cap_item["capId"] = (integer) $cap_item["capId"];	
				$cap_item["quantity"] = (integer) $cap_item["quantity"];	
				
				$sql .= $next_order_id . "," . $cap_item["capId"] . "," . $cap_item["quantity"] . ")";
			}
			
			$sql .= ";";
			
			$this->_conn->query($sql);
			
			if (!$this->_conn->commit())
			{
				//TODO: should stop, rollback and redirect to error page if an error occurs.
			}
			
			$this->_closeConnection();
		}
	}
	
	
	/*
		generate a new customer. It is assumed the login and email are unique, but this is not constrained.
	*/
	public function insertCustomer($first_name, $last_name, $login, $password_hash, $email, $home_number, 
									$work_number, $mobile_number, $street_address, $suburb, $city)
	{
		$this->_openConnection();	
		
		$first_name = $this->_conn->real_escape_string($first_name);	
		$last_name = $this->_conn->real_escape_string($last_name);	
		$login = $this->_conn->real_escape_string($login);	
		$email = $this->_conn->real_escape_string($email);	
		$home_number = $this->_conn->real_escape_string($home_number);	
		$work_number = $this->_conn->real_escape_string($work_number);	
		$mobile_number = $this->_conn->real_escape_string($mobile_number);	
		$street_address = $this->_conn->real_escape_string($street_address);	
		$suburb = $this->_conn->real_escape_string($suburb);	
		$city = $this->_conn->real_escape_string($city);	
		$password_hash = $this->_conn->real_escape_string($password_hash);	
		
		$this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
		
		$sql =  "insert into SiteUser (firstName, lastName, login, passwordhash, emailAddress, homenumber, worknumber, mobilenumber," .
				" streetaddress, suburb, city) values ('".$first_name."','".$last_name."','".$login."','".$password_hash."','".$email."','" .
				$home_number."','".$work_number."','".$mobile_number."','".$street_address."','".$suburb."','".$city."');";
		$this->_conn->query($sql);
		
		if (!$this->_conn->commit())
		{
			//TODO: should stop, rollback and redirect to error page if an error occurs.
		}
		
		$this->_closeConnection();								
	}
	
	
	/*
		update an existing customer. It is assumed the login and email are unique, but this is not constrained.
		The password is not updated here.
	*/
	public function updateCustomerButNotPassword($first_name, $last_name, $login, $email, $home_number, 
									$work_number, $mobile_number, $street_address, $suburb, $city, $id)
	{
		$this->_openConnection();
		
		$first_name = $this->_conn->real_escape_string($first_name);	
		$last_name = $this->_conn->real_escape_string($last_name);	
		$login = $this->_conn->real_escape_string($login);	
		$email = $this->_conn->real_escape_string($email);	
		$home_number = $this->_conn->real_escape_string($home_number);	
		$work_number = $this->_conn->real_escape_string($work_number);	
		$mobile_number = $this->_conn->real_escape_string($mobile_number);	
		$street_address = $this->_conn->real_escape_string($street_address);	
		$suburb = $this->_conn->real_escape_string($suburb);	
		$city = $this->_conn->real_escape_string($city);	
		$id = (integer) $id;
			
		$this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
		
		$sql =  "update SiteUser set firstName='".$first_name."', set lastName='".$last_name."', set login='".$login."'," .
		" set emailAddress='".$email."', set homenumber='".$home_number."', set worknumber='".$work_number."', set mobilenumber='".$mobile_number."',". 
		" set streetaddress='".$street_address."', set suburb='".$suburb."', set city='".$city."' " .
		" where id=" . $id . ";";
		$this->_conn->query($sql);
		
		if (!$this->_conn->commit())
		{
			//TODO: should stop, rollback and redirect to error page if an error occurs.
		}
		
		$this->_closeConnection();								
	}	
	
	/*
		update an existing customer's password hash.
	*/
	public function updateCustomerPasswordOnly($hash, $id)
	{
		$this->_openConnection();	
		
		$id = (integer) $id;
		$hash = $this->_conn->real_escape_string($hash);	
		
		$this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
		
		$sql =  "update SiteUser set passwordhash='".$hash."' where id=" . $id . ";";
		$this->_conn->query($sql);
		
		if (!$this->_conn->commit())
		{
			//TODO: should stop, rollback and redirect to error page if an error occurs.
		}
		
		$this->_closeConnection();								
	}
	
	/*
		request one customer, using an id.
	*/
	public function selectSingleCustomer( $id)
	{
		$this->_openConnection();	
		
		$id = (integer) $id;
		
		if (!$query_result = $this->_conn->query("Select * from SiteUser where UserType='C' and id=" . $id . ";"))
		{
			//TODO: should stop, rollback and redirect to error page if an error occurs.
		}
		
		// if cannot find such a customer, return an empty array.
		$customer = array();
		
		if ($query_result->num_rows > 0)
		{
			// only fetch the first customer found, if multiple customers found.
			// as ID is PK and unique, assumed that multiple customers will never be returned.
			
			$customer = $query_result->fetch_assoc();
		}
		
		if ($query_result)
		{
			$query_result->free();
		}
		
		$this->_closeConnection();	
		
		return $customer;	
	}
	
	/*
		request one customer, using a login.
	*/
	public function selectSingleCustomerByLogin( $login)
	{
		$this->_openConnection();	
		
		$login = $this->_conn->real_escape_string($login);	
		
		if (!$query_result = $this->_conn->query("Select * from SiteUser where UserType='C' and login='" . $login . "';"))
		{
			//TODO: should stop, rollback and redirect to error page if an error occurs.
		}
		
		// of cannot find such a customer, return an empty array.
		$customer = array();
		
		if ($query_result->num_rows > 0)
		{
			// only fetch the first customer found, if multiple customers found.
			// as ID is PK and unique, assumed that multiple customers will never be returned.
			
			$customer = $query_result->fetch_assoc();
		}
		
		if ($query_result)
		{
			$query_result->free();
		}
		
		$this->_closeConnection();	
		
		return $customer;	
	}
	
	/*
		check for matching customer, using a login or email
	*/
	public function matchCustomerByLogin( $login)
	{
		$this->_openConnection();
		
		$login = $this->_conn->real_escape_string($login);		
		
		if (!$query_result = $this->_conn->query("Select * from SiteUser where UserType='C' and login='" . $login . "';"))
		{
			//TODO: should stop, rollback and redirect to error page if an error occurs.
		}
		
		// of cannot find such a customer, return an empty array.
		$match = false;
		
		if ($query_result->num_rows > 0)
		{
			$match = true;
		}
		
		if ($query_result)
		{
			$query_result->free();
		}
		
		$this->_closeConnection();	
		
		return $match;	
	}
	
	/*
		check for matching customer, using a login or email
	*/
	public function matchCustomerByEmail( $email)
	{
		$this->_openConnection();
		
		$email = $this->_conn->real_escape_string($email);	
		
		if (!$query_result = $this->_conn->query("Select * from SiteUser where UserType='C' and emailAddress='" . $email . "';"))
		{
			//TODO: should stop, rollback and redirect to error page if an error occurs.
		}
		
		// of cannot find such a customer, return an empty array.
		$match = false;
		
		if ($query_result->num_rows > 0)
		{
			$match = true;
		}
		
		if ($query_result)
		{
			$query_result->free();
		}
		
		$this->_closeConnection();	
		
		return $match;	
	}
	
	/*
		request one customer, using an id.
	*/
	public function selectSingleAdmin( $id)
	{
		$this->_openConnection();	
		
		$id = (integer) $id;
		
		if (!$query_result = $this->_conn->query("Select * from SiteUser where UserType='A' and id=" . $id . ";"))
		{
			//TODO: should stop, rollback and redirect to error page if an error occurs.
		}
		
		// of cannot find such a customer, return an empty array.
		$customer = array();
		
		if ($query_result->num_rows > 0)
		{
			// only fetch the first customer found, if multiple customers found.
			// as ID is PK and unique, assumed that multiple customers will never be returned.
			
			$customer = $query_result->fetch_assoc();
		}
		
		if ($query_result)
		{
			$query_result->free();
		}
		
		$this->_closeConnection();	
		
		return $customer;	
	}
	
	/*
		request one customer, using an id.
	*/
	public function selectSingleAdminByLogin( $login)
	{
		$this->_openConnection();	
		
		$login = $this->_conn->real_escape_string($login);	
		
		if (!$query_result = $this->_conn->query("Select * from SiteUser where UserType='A' and login='" . $login . "';"))
		{
			//TODO: should stop, rollback and redirect to error page if an error occurs.
		}
		
		// of cannot find such a customer, return an empty array.
		$customer = array();
		
		if ($query_result->num_rows > 0)
		{
			// only fetch the first customer found, if multiple customers found.
			// as ID is PK and unique, assumed that multiple customers will never be returned.
			
			$customer = $query_result->fetch_assoc();
		}
		
		if ($query_result)
		{
			$query_result->free();
		}
		
		$this->_closeConnection();	
		
		return $customer;	
	}
	
	/*
		check for matching customer, using a login or email
	*/
	public function matchAdminByLogin( $login)
	{
		$this->_openConnection();	
		
		$login = $this->_conn->real_escape_string($login);	
		
		if (!$query_result = $this->_conn->query("Select * from SiteUser where UserType='A' and login='" . $login . "';"))
		{
			//TODO: should stop, rollback and redirect to error page if an error occurs.
		}
		
		// of cannot find such a customer, return an empty array.
		$match = false;
		
		if ($query_result->num_rows > 0)
		{
			$match = true;
		}
		
		if ($query_result)
		{
			$query_result->free();
		}
		
		$this->_closeConnection();	
		
		return $match;	
	}
	
	/*
		get all categories with associated products. use LIMIT.
	*/
	public function selectAvailableCategoriesWithLimit( $limit_start,  $limit_length)
	{
		$limit_start = (integer) $limit_start;
		$limit_length = (integer) $limit_length;
		
		if ($limit_length < 1 ) 
		{
			$limit_length = 1;
		}
		if ($limit_start < 0 ) 
		{
			$limit_start = 0;
		}
		
		$this->_openConnection();	
		
		if (!$query_result = $this->_conn->query("Select * from `category` WHERE `id` in (select distinct `categoryId` from `cap`) order by id," .
		" name LIMIT " . $limit_start . ", " . $limit_length . ";"))
		{
			//TODO: should stop, rollback and redirect to error page if an error occurs.

		}
		
		$available_categories = array();
		
		if ($query_result->num_rows > 0)
		{
			while ($row = $query_result->fetch_assoc())
			{
					$available_categories[] = $row;
			}
		}
		
		if ($query_result)
		{
			$query_result->free();
		}
		
		$this->_closeConnection();	
		
		return $available_categories;	
	}
	
	/*
		get all products for a category, using the categoryId. use LIMIT.
	*/
	public function selectCapsbyCategoryIdWithLimit( $categoryId,  $limit_start,  $limit_length)
	{
		$limit_start = (integer) $limit_start;
		$limit_length = (integer) $limit_length;
		$categoryId = (integer) $categoryId;
		
		if ($limit_length < 1) 
		{
			$limit_length = 1;
		}
		if ($limit_start < 0 ) 
		{
			$limit_start = 0;
		}
		
		$this->_openConnection();	
		
		if (!$query_result = $this->_conn->query("Select * from `cap` WHERE `categoryId` = " . $categoryId . " order by categoryId, id LIMIT "
		. $limit_start . ", " . $limit_length . ";"))
		{
			//TODO: should stop, rollback and redirect to error page if an error occurs.
		}
		
		$caps = array();
		
		if ($query_result->num_rows > 0)
		{
			while ($row = $query_result->fetch_assoc())
			{
					$caps[] = $row;
			}
		}
		
		if ($query_result)
		{
			$query_result->free();
		}
		
		$this->_closeConnection();	
		
		return $caps;	
	}
	
	/*
		get a single cap.
	*/
	public function selectSingleCap( $capId)
	{
		$capId = (integer) $capId;
		
		$this->_openConnection();	
		
		if (!$query_result = $this->_conn->query("Select * from `cap` WHERE `id` = " . $capId . ";"))
		{
			//TODO: should stop, rollback and redirect to error page if an error occurs.
		}
		
		$cap = array();
		
		if ($query_result->num_rows > 0)
		{
			$cap = $query_result->fetch_assoc();
		}
		
		if ($query_result)
		{
			$query_result->free();
		}
		
		$this->_closeConnection();	
		
		return $cap;	
	}
	
	/*
		get all orders and orderitems for a customer. use LIMIT.
	*/
	public function selectOrdersWithItemsByCustomer( $customerId,  $limit_start,  $limit_length)
	{
		$limit_start = (integer) $limit_start;
		$limit_length = (integer) $limit_length;
		$customerId = (integer) $customerId;
		
		$this->_openConnection();	
		
		if (!$query_result = $this->_conn->query("Select id, userId, status, datePlaced, capId, quantity from `CustomerOrder` co JOIN `OrderItem`" .
						" oi ON oi.`OrderId`=co.`id` WHERE userId=" . $customerId . " order by status, datePlaced, capId, quantity;"))
		{
			//TODO: should stop, rollback and redirect to error page if an error occurs.
		}
		
		$orders = array();
		
		if ($query_result->num_rows > 0)
		{
			while ($row = $query_result->fetch_assoc())
			{
				$orders[] = $row;			
			}
		}
		
		if ($query_result)
		{
			$query_result->free();
		}
		
		$this->_closeConnection();	
		
		return $orders;	
	}
}