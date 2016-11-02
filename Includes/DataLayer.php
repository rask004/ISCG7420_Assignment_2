<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 11:24 PM
 */

namespace DataLayer;

require_once('Session.php');

// Database interaction object
class DataManager
{
	// Stores the mysqli connection
	private $_conn;
	
	/*
		construct helper function, build any tables if not present.
	*/
	private function _BuildTables()
	{
		$this->_OpenConnection();
		
		if(!$this->_conn->query("create table if not exists `SiteUser`(`id` int UNSIGNED AUTO_INCREMENT primary key, `login`   varchar(64)    not null, " .
                            "`passwordhash`    varchar(64)    not null, `userType`    char(1)     not null, `emailAddress`    varchar(100)   not null, " .
                            "`homeNumber`  varchar(11), `workNumber`  varchar(11), `mobileNumber`    varchar(14), `firstName`   varchar(32), " .
                            "`lastName`    varchar(32), `streetAddress`   varchar(64), `suburb`      varchar(24), `city`        varchar(16), " .
                            "`isDisabled`  BIT(1)    not null DEFAULT 0 );"))
		{
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SiteUser table Generation";
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		

        if(!$this->_conn->query("create table if not exists CustomerOrder(`id` int UNSIGNED AUTO_INCREMENT primary key, `userId` int UNSIGNED not null, " .
                           "`status` varchar(7) not null DEFAULT 'waiting', `datePlaced` datetime not null, ".
						   "CONSTRAINT fk_OrderCustomer Foreign Key (`userId`) References `SiteUser`(`id`));"))
		{
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; CustomerOrder table Generation";
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		

        if(!$this->_conn->query("create table if not exists `Supplier`(`id` int UNSIGNED AUTO_INCREMENT primary key, `name` varchar(32) not null, " .
                            "`homeNumber`   varchar(11) null, `worknumber` varchar(11) null, " .
                            "`mobileNumber` varchar(13) null, `emailAddress` varchar(64) not null);"))
		{
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; Supplier table Generation";
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		

        if(!$this->_conn->query("create table if not exists `Category`(`id` int UNSIGNED AUTO_INCREMENT primary key, `name` varchar(40) not null); "))
		{
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; Category table Generation";
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		

        if(!$this->_conn->query("create table if not exists `Cap`(`id` int UNSIGNED AUTO_INCREMENT primary key, `name`    varchar(40)    not null, " .
                            "`price` real UNSIGNED not null, `description` varchar(512)   not null, `imageUrl` varchar(96) not null, " .
                            "`supplierId` int UNSIGNED not null,    " .
                            "`categoryId` int UNSIGNED not null,    " . 
							"CONSTRAINT fk_supplier Foreign Key (`supplierId`) References  `Supplier`(`id`), " .
							"CONSTRAINT fk_category Foreign Key (`categoryId`) References `Category`(`id`)); "))
		{
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; Cap table Generation";
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		

        if(!$this->_conn->query("create table if not exists `OrderItem`(`orderId` int UNSIGNED not null , " .
                                                       "`capId` int UNSIGNED not null, " .
                                                       "`quantity` smallint UNSIGNED not null, " .
                                                       "Constraint orderItem_pk Primary Key(`capId`, `orderId`), ".
													   "CONSTRAINT fk_OrderOrderItem Foreign Key (`orderId`) References `CustomerOrder`(`id`), ". 
													   "CONSTRAINT fk_capOrderItem Foreign Key (`capId`) References `Cap`(`id`)); "))
		{
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; OrderItem table Generation";
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
														  
		$this->_CloseConnection();
	}
	
	/*
		helper method, open and prepare connection.
	*/
	private function _OpenConnection()
	{
		$this->_conn = new \mysqli("localhost", "askewr04", "29101978", "askewr04mysql3");
		if ($this->_conn->connect_errno) 
		{
			$_SESSION["last_Error"] = "DB_connection";
			$_SESSION["Error_MSG"] = (string) $mysqli->connect_errno . "; " . $mysqli->connect_error;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error.php");
			exit;
		}
		$this->_conn->set_charset('utf8');	
	}
	
	/*
		helper method, close the connection.
	*/
	private function _CloseConnection()
	{
		$this->_conn->close();
	}
	
	/*
		constructor
		rebuilds non-existent tables as necessary
		assumes the tables will remain unaltered throughout the application instance lifecycle.
	*/
	function __construct()
	{
		$this->_BuildTables();
	}
	
	
	/*
		given a customer id and a list of caps with quantities, insert a new order.
		Only the cap Id and quantity is required for each order item. thus for simplicity, treat 
			capIds as array keys and quantities as array values.
	*/
	public function InsertOrder( $customer_id, array $cap_quantity_list) 
	{		
		// there must be caps to generate orderitems from. if not, do nothing.
		if ( count( $cap_quantity_list) > 0 ) 
		{		
			$this->_OpenConnection();
			
			$id = (integer) $customer_id;
			
			// need to know the orderid. request a new order id first.
			$result = $this->_conn->query("SHOW TABLE STATUS LIKE 'CustomerOrder'");
			$data = $result->fetch_assoc();
			$next_order_id = $data['Auto_increment'];
			
			$now = new \DateTime();
			
			$this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
			// create the customer order.
			$sql = "insert into CustomerOrder (id, userId, datePlaced) values (".$next_order_id.",".$customer_id.",'".$now->format('Y-m-d H:i:s')."');";
			if (!$this->_conn->query($sql))
			{
				$this->_conn->rollback();
				$_SESSION["last_Error"] = "DB_Error_Generic";
				$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL='". $sql ."'";
				header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error.php");
				exit;
			}

			// create each new order item, using the order id.
			$sql = "insert into OrderItem (orderId, capId, quantity ) values ";			
			$first_item = true;
			foreach ($cap_quantity_list as $capId => $quantity)
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
					
				$capId = (integer) $capId;	
				$quantity = (integer) $quantity;	
				
				$sql .= $next_order_id . "," . $capId . "," . $quantity . ")";
			}
			
			$sql .= ";";
			
			$this->_conn->query($sql);
			
			if (!$this->_conn->commit())
			{
				$this->_conn->rollback();
				$_SESSION["last_Error"] = "DB_Error_Generic";
				$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL=". $sql;
				header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
				exit;
			}
			
			$this->_CloseConnection();
		}
	}
	
	/*
		generate a new customer. It is assumed the login and email are unique, but this is not constrained.
	*/
	public function InsertCustomer($first_name, $last_name, $login, $salt, $password_hash, $email, $home_number, 
									$work_number, $mobile_number, $street_address, $suburb, $city)
	{
		$this->_OpenConnection();	
		
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
		$salt = $this->_conn->real_escape_string($salt);	
		
		$this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
		
		$sql =  "insert into SiteUser (userType, firstName, lastName, login, passwordsalt, passwordhash, emailAddress," .
				" homenumber, worknumber, mobilenumber, streetaddress, suburb, city) values " .
				"('C', '".$first_name."','".$last_name."','".$login."','".$salt."','".$password_hash."','".$email.
				"','" .$home_number."','".$work_number."','".$mobile_number."','".$street_address."','".$suburb."','".$city."');";
		$this->_conn->query($sql);
		
		if (!$this->_conn->commit())
		{
			$this->_conn->rollback();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		
		$this->_CloseConnection();								
	}
	
	
	/*
		update an existing customer. It is assumed the login and email are unique, but this is not constrained.
		The password is not updated here.
	*/
	public function UpdateCustomerButNotPassword($first_name, $last_name, $login, $email, $home_number, 
									$work_number, $mobile_number, $street_address, $suburb, $city, $id)
	{
		$this->_OpenConnection();
		
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
		" where userType='C' AND id=" . $id . ";";
		$this->_conn->query($sql);
		
		if (!$this->_conn->commit())
		{
			$this->_conn->rollback();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		
		$this->_CloseConnection();								
	}	
	
	/*
		update an existing customer's password hash.
	*/
	public function UpdateCustomerPasswordOnly($salt, $hash, $id)
	{
		$this->_OpenConnection();	
		
		$id = (integer) $id;
		$salt = $this->_conn->real_escape_string($salt);	
		$hash = $this->_conn->real_escape_string($hash);	
		
		$this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
		
		$sql =  "update SiteUser set passwordsalt='".$salt."', passwordhash='".$hash."' where userType='C' AND id=" . $id . ";";
		$this->_conn->query($sql);
		
		if (!$this->_conn->commit())
		{
			$this->_conn->rollback();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		
		$this->_CloseConnection();								
	}
	
	/*
		request one customer, using an id.
	*/
	public function SelectSingleCustomer( $id)
	{
		$this->_OpenConnection();	
		
		$id = (integer) $id;
		
		if (!$query_result = $this->_conn->query("Select * from SiteUser where UserType='C' and id=" . $id . ";"))
		{
			$this->_conn->rollback();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
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
		
		$this->_CloseConnection();	
		
		return $customer;	
	}
	
	/*
		request one customer, using a login.
	*/
	public function SelectSingleCustomerByLogin( $login)
	{
		$this->_OpenConnection();	
		
		$login = $this->_conn->real_escape_string($login);	
		
		if (!$query_result = $this->_conn->query("Select * from SiteUser where UserType='C' and login='" . $login . "';"))
		{
			$this->_conn->rollback();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
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
		
		$this->_CloseConnection();	
		
		return $customer;	
	}
	
	/*
		check for matching customer, using a login
	*/
	public function MatchCustomerByLogin( $login)
	{
		$this->_OpenConnection();
		
		$login = $this->_conn->real_escape_string($login);		
		
		if (!$query_result = $this->_conn->query("Select * from SiteUser where UserType='C' and login='" . $login . "';"))
		{
			$this->_conn->rollback();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		
		$match = false;
		
		if ($query_result->num_rows > 0)
		{
			$match = true;
		}
		
		if ($query_result)
		{
			$query_result->free();
		}
		
		$this->_CloseConnection();	
		
		return $match;	
	}
	
	/*
		check for matching customer, using email
	*/
	public function MatchCustomerByEmail( $email)
	{
		$this->_OpenConnection();
		
		$email = $this->_conn->real_escape_string($email);	
		
		if (!$query_result = $this->_conn->query("Select * from SiteUser where UserType='C' and emailAddress='" . $email . "';"))
		{
			$this->_conn->rollback();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		
		$match = false;
		
		if ($query_result->num_rows > 0)
		{
			$match = true;
		}
		
		if ($query_result)
		{
			$query_result->free();
		}
		
		$this->_CloseConnection();	
		
		return $match;	
	}
	
	/*
		request one customer, using an id.
	*/
	public function SelectSingleAdmin( $id)
	{
		$this->_OpenConnection();	
		
		$id = (integer) $id;
		
		if (!$query_result = $this->_conn->query("Select * from SiteUser where UserType='A' and id=" . $id . ";"))
		{
			$this->_conn->rollback();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
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
		
		$this->_CloseConnection();	
		
		return $customer;	
	}
	
	/*
		request one customer, using an id.
	*/
	public function SelectSingleAdminByLogin( $login)
	{
		$this->_OpenConnection();	
		
		$login = $this->_conn->real_escape_string($login);	
		
		if (!$query_result = $this->_conn->query("Select * from SiteUser where UserType='A' and login='" . $login . "';"))
		{
			$this->_conn->rollback();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
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
		
		$this->_CloseConnection();	
		
		return $customer;	
	}
	
	/*
		check for matching customer, using a login or email
	*/
	public function MatchAdminByLogin( $login)
	{
		$this->_OpenConnection();	
		
		$login = $this->_conn->real_escape_string($login);	
		
		if (!$query_result = $this->_conn->query("Select * from SiteUser where UserType='A' and login='" . $login . "';"))
		{
			$this->_conn->rollback();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
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
		
		$this->_CloseConnection();	
		
		return $match;	
	}
	
	/*
		get all categories with associated products. use LIMIT.
	*/
	public function SelectAvailableCategoriesWithLimit( $limit_start,  $limit_length)
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
		
		$this->_OpenConnection();	
		
		if (!$query_result = $this->_conn->query("SELECT c.id, c.name, cp.imageUrl FROM `cap` cp, `category` c WHERE cp.categoryId = c.id group by c.id ".  			"order by id, name LIMIT " . $limit_start . ", " . $limit_length . ";"))
		{
			$this->_conn->rollback();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
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
		
		$this->_CloseConnection();	
		
		return $available_categories;	
	}
	
	/*
		get a count of all categories associated with caps
	*/
	public function SelectCountOfAvailableCategories()
	{
		$this->_OpenConnection();	
		
		if (!$query_result = $this->_conn->query("Select * from `category` WHERE `id` in (select distinct `categoryId` from `cap`);"))
		{
			$this->_conn->rollback();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		
		$categoryCount = $query_result->num_rows;
		
		if ($query_result)
		{
			$query_result->free();
		}
		
		$this->_CloseConnection();	
		
		return $categoryCount;	
	}
	
	
	/*
		get all products for a category, using the categoryId. use LIMIT.
	*/
	public function SelectCapsbyCategoryIdWithLimit( $categoryId,  $limit_start,  $limit_length)
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
		
		$this->_OpenConnection();	
		
		if (!$query_result = $this->_conn->query("Select * from `cap` WHERE `categoryId` = " . $categoryId . " order by categoryId, id LIMIT "
		. $limit_start . ", " . $limit_length . ";"))
		{
			$this->_conn->rollback();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
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
		
		$this->_CloseConnection();	
		
		return $caps;	
	}
	
	/*
		get all products for a category, using the categoryId. use LIMIT.
	*/
	public function SelectCountOfCapsbyCategoryId( $categoryId)
	{
		$categoryId = (integer) $categoryId;
		
		$this->_OpenConnection();	
		
		if (!$query_result = $this->_conn->query("Select * from `cap` WHERE `categoryId` = " . $categoryId . ";"))
		{
			$this->_conn->rollback();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		
		$capCount = $query_result->num_rows;
		
		if ($query_result)
		{
			$query_result->free();
		}
		
		$this->_CloseConnection();
		
		return $capCount;	
	}
	
	/*
		get all products for a category, using the categoryId. use LIMIT.
	*/
	public function SelectAllCaps($limit_start,  $limit_length)
	{
		$this->_OpenConnection();	
		
		if (!$query_result = $this->_conn->query("Select * from `cap` order by id LIMIT ".$limit_start.", ".$limit_length.";"))
		{
			$this->_conn->rollback();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
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
		
		$this->_CloseConnection();	
		
		return $caps;	
	}
	
	/*
		get all products for a category, using the categoryId. use LIMIT.
	*/
	public function SelectCountOfAllCaps()
	{
		$this->_OpenConnection();	
		
		if (!$query_result = $this->_conn->query("Select * from `cap`;"))
		{
			$this->_conn->rollback();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		
		$capcount = $query_result->num_rows;
		
		if ($query_result)
		{
			$query_result->free();
		}
		
		$this->_CloseConnection();	
		
		return $capcount;	
	}
	
	/*
		get a single cap.
	*/
	public function SelectSingleCap( $capId)
	{
		$capId = (integer) $capId;
		
		$this->_OpenConnection();	
		
		if (!$query_result = $this->_conn->query("Select * from `cap` WHERE `id` = " . $capId . ";"))
		{
			$this->_conn->rollback();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
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
		
		$this->_CloseConnection();	
		
		return $cap;	
	}
	
	/*
		get all orders and orderitems for a customer. use LIMIT.
	*/
	public function SelectOrdersWithItemsByCustomer( $customerId,  $limit_start,  $limit_length)
	{
		$limit_start = (integer) $limit_start;
		$limit_length = (integer) $limit_length;
		$customerId = (integer) $customerId;
		
		$this->_OpenConnection();	
		
		if (!$query_result = $this->_conn->query("Select id, userId, status, datePlaced, capId, quantity from `CustomerOrder` co JOIN `OrderItem`" .
						" oi ON oi.`OrderId`=co.`id` WHERE userId=" . $customerId . " order by status, datePlaced, capId, quantity limit " .
						$limit_start . ", " . $limit_length . ";"))
		{
			$this->_conn->rollback();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
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
		
		$this->_CloseConnection();	
		
		return $orders;	
	}
	
	/*
		get all orders and orderitems for a customer. use LIMIT.
	*/
	public function SelectOrderSummariesByCustomer( $customerId,  $limit_start,  $limit_length)
	{
		$limit_start = (integer) $limit_start;
		$limit_length = (integer) $limit_length;
		$customerId = (integer) $customerId;
		
		$this->_OpenConnection();	
		
		if (!$query_result = $this->_conn->query("SELECT co.id as id, co.status as status, co.datePlaced as datePlaced, ".
		" sum(oi.quantity) as totalQuantity, sum(oi.quantity * c.price) as totalPrice FROM `orderitem` oi, `customerorder` co, ".
		" `cap` c WHERE userId=" . $customerId . " and oi.orderid = co.id AND c.id = oi.capId group by orderId " .
		" order by co.status, co.datePlaced, co.id limit " . $limit_start . ", " . $limit_length . ";"))
		{
			$this->_conn->rollback();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		
		$summaries = array();
		
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
		
		$this->_CloseConnection();	
		
		return $orders;	
	}
	
	/*
		get count of order summaries
	*/
	public function GetCountOfOrderSummariesByCustomer($customerId)
	{
		$customerId = (integer) $customerId;
		
		$this->_OpenConnection();	
		
		if (!$query_result = $this->_conn->query("SELECT co.id as id, ".
		" sum(oi.quantity) as totalQuantity, sum(oi.quantity * c.price) as totalPrice FROM `orderitem` oi, `customerorder` co, ".
		" `cap` c WHERE userId=" . $customerId . " and oi.orderid = co.id AND c.id = oi.capId group by orderId ;"))
		{
			$this->_conn->rollback();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		
		$summaryCount = $query_result->num_rows;
		
		if ($query_result)
		{
			$query_result->free();
		}
		
		$this->_CloseConnection();	
		
		return $summaryCount;	
	}
	
	/*
		check a given password salt is not in use
	*/
	public function MatchesUsedSalt($salt)
	{
		$this->_OpenConnection();	
		
		$salt = $this->_conn->real_escape_string($salt);
		
		$matches = false;	
		
		if (!$query_result = $this->_conn->query("Select 1 FROM `SiteUser` WHERE passwordsalt='" . $salt . "';"))
		{
			$this->_conn->rollback();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		
		if ($query_result->num_rows > 0)
		{
			$matches = true;
		}
		
		if ($query_result)
		{
			$query_result->free();
		}
		
		$this->_CloseConnection();	
		
		return $matches;
	}
	
	/*
		given a customer login, retrieve the salt and hash for this user.
	*/
	public function RequestAdminPasswordSaltAndHash($login)
	{
		$this->_OpenConnection();
		
		$data = array();
		
		$login = $this->_conn->real_escape_string($login);
		
		if (!$query_result = $this->_conn->query("Select passwordsalt, passwordhash FROM `SiteUser` where UserType='A' and login='" . $login . "';"))
		{
			$this->_conn->rollback();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		
		if ($query_result->num_rows > 0)
		{
			$data = $query_result->fetch_assoc();
		}
		
		if ($query_result)
		{
			$query_result->free();
		}
		
		$this->_CloseConnection();	
		
		return $data;
	}
	
	/*
		given a customer login, retrieve the salt and hash for this user.
	*/
	public function RequestCustomerPasswordSaltAndHash($login)
	{
		$this->_OpenConnection();
		
		$data = array();
		
		$login = $this->_conn->real_escape_string($login);
		
		if (!$query_result = $this->_conn->query("Select * FROM `siteuser` WHERE userType='C' and login='" . $login . "';"))
		{
			$this->_conn->rollback();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $mysqli->_conn->errno . "; " . $mysqli->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		
		if ($query_result->num_rows > 0)
		{
			$data = $query_result->fetch_assoc();
		}
		
		if ($query_result)
		{
			$query_result->free();
		}
		
		$this->_CloseConnection();	
		
		return $data;
	}
}