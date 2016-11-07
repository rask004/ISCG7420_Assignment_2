<?php
/**
 * Created by PhpStorm.
 * User: Roland
 * Date: 11/10/2016
 * Time: 11:24 PM
 */

namespace DataLayer;


require_once('Session.php');
require_once('Common.php');

use Common\Logging as Logging;

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
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SiteUser table Generation";
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		

        if(!$this->_conn->query("create table if not exists CustomerOrder(`id` int UNSIGNED AUTO_INCREMENT primary key, `userId` int UNSIGNED not null, " .
                           "`status` varchar(7) not null DEFAULT 'waiting', `datePlaced` datetime not null, ".
						   "CONSTRAINT fk_OrderCustomer Foreign Key (`userId`) References `SiteUser`(`id`));"))
		{
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; CustomerOrder table Generation";
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		

        if(!$this->_conn->query("create table if not exists `Supplier`(`id` int UNSIGNED AUTO_INCREMENT primary key, `name` varchar(32) not null, " .
                            "`homeNumber`   varchar(11) null, `worknumber` varchar(11) null, " .
                            "`mobileNumber` varchar(13) null, `emailAddress` varchar(64) not null);"))
		{
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; Supplier table Generation";
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		

        if(!$this->_conn->query("create table if not exists `Category`(`id` int UNSIGNED AUTO_INCREMENT primary key, `name` varchar(40) not null); "))
		{
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; Category table Generation";
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
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; Cap table Generation";
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
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; OrderItem table Generation";
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
														  
		$this->_CloseConnection();
        
        Logging::Log('DataManager, completed building tables.');
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
			$_SESSION["Error_MSG"] = (string) $this->_conn->connect_errno . "; " . $this->_conn->connect_error;
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
	public function InsertOrder( $customerId, array $cap_quantity_list) 
	{		
		// there must be caps to generate orderitems from. if not, do nothing.
		if ( count( $cap_quantity_list) > 0 ) 
		{		
			$this->_OpenConnection();
			
			$id = (integer) $customerId;
			
			// need to know the orderid. request a new order id first.
			$result = $this->_conn->query("SHOW TABLE STATUS LIKE 'CustomerOrder'");
			$data = $result->fetch_assoc();
			$next_order_id = $data['Auto_increment'];
			
			$now = new \DateTime();
			
			$this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
			// create the customer order.
			$sql = "insert into CustomerOrder (id, userId, datePlaced) values (".$next_order_id.",".$customerId.",'".$now->format('Y-m-d H:i:s')."');";
			if (!$this->_conn->query($sql))
			{
				$this->_conn->rollback();
                $this->_CloseConnection();
				$_SESSION["last_Error"] = "DB_Error_Generic";
				$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL='". $sql ."'";
				header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error.php");
				exit;
			}

            Logging::Log('DataManager, query: ' . $sql  . "\r\n");

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
                $this->_CloseConnection();
				$_SESSION["last_Error"] = "DB_Error_Generic";
				$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
				header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
				exit;
			}

            Logging::Log('DataManager, query: ' . $sql  . "\r\n");
			
			$this->_CloseConnection();
		}
	}
	
	/*
		generate a new customer. It is assumed the login and email are unique, but this is not constrained.
	*/
	public function InsertCustomer($firstName, $lastName, $login, $salt, $password_hash, $email, $homeNumber, 
									$workNumber, $mobileNumber, $streetAddress, $suburb, $city)
	{
		$this->_OpenConnection();	
		
		$firstName = $this->_conn->real_escape_string($firstName);	
		$lastName = $this->_conn->real_escape_string($lastName);	
		$login = $this->_conn->real_escape_string($login);	
		$email = $this->_conn->real_escape_string($email);	
		$homeNumber = $this->_conn->real_escape_string($homeNumber);	
		$workNumber = $this->_conn->real_escape_string($workNumber);	
		$mobileNumber = $this->_conn->real_escape_string($mobileNumber);	
		$streetAddress = $this->_conn->real_escape_string($streetAddress);	
		$suburb = $this->_conn->real_escape_string($suburb);	
		$city = $this->_conn->real_escape_string($city);	
		$password_hash = $this->_conn->real_escape_string($password_hash);	
		$salt = $this->_conn->real_escape_string($salt);	
		
		$this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
		
		$sql =  "insert into SiteUser (userType, firstName, lastName, login, passwordsalt, passwordhash, emailAddress," .
				" homenumber, worknumber, mobilenumber, streetaddress, suburb, city) values " .
				"('C', '".$firstName."','".$lastName."','".$login."','".$salt."','".$password_hash."','".$email.
				"','" .$homeNumber."','".$workNumber."','".$mobileNumber."','".$streetAddress."','".$suburb."','".$city."');";
		$this->_conn->query($sql);
		
		if (!$this->_conn->commit())
		{
			$this->_conn->rollback();
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");
		
		$this->_CloseConnection();								
	}
	
	
	/*
		update an existing customer. It is assumed the login and email are unique, but this is not constrained.
		The password is not updated here.
	*/
	public function UpdateCustomerButNotPassword($firstName, $lastName, $login, $email, $homeNumber, 
									$workNumber, $mobileNumber, $streetAddress, $suburb, $city, $id)
	{
		$this->_OpenConnection();
		
		$firstName = $this->_conn->real_escape_string($firstName);	
		$lastName = $this->_conn->real_escape_string($lastName);	
		$login = $this->_conn->real_escape_string($login);	
		$email = $this->_conn->real_escape_string($email);	
		$homeNumber = $this->_conn->real_escape_string($homeNumber);	
		$workNumber = $this->_conn->real_escape_string($workNumber);	
		$mobileNumber = $this->_conn->real_escape_string($mobileNumber);	
		$streetAddress = $this->_conn->real_escape_string($streetAddress);	
		$suburb = $this->_conn->real_escape_string($suburb);	
		$city = $this->_conn->real_escape_string($city);	
		$id = (integer) $id;
			
		$this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
		
		$sql =  "update SiteUser set firstName='".$firstName."', lastName='".$lastName."', login='".$login."'," .
		" emailAddress='".$email."', homenumber='".$homeNumber."', worknumber='".$workNumber."', mobilenumber='".$mobileNumber."',". 
		" streetaddress='".$streetAddress."', suburb='".$suburb."', city='".$city."' " .
		" where userType='C' AND id=" . $id . ";";
		
		$this->_conn->query($sql);
		
		if (!$this->_conn->commit())
		{
			$this->_conn->rollback();
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");
		
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
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");
		
		$this->_CloseConnection();								
	}
	
	/*
		request one customer, using an id.
	*/
	public function SelectSingleCustomer( $id)
	{
		$this->_OpenConnection();	
		
		$id = (integer) $id;

        $sql = "Select * from SiteUser where UserType='C' and id=" . $id . ";";
		
		if (!$query_result = $this->_conn->query($sql))
		{
			$this->_conn->rollback();
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
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

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");
		
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

        $sql = "Select * from SiteUser where UserType='C' and login='" . $login . "';";
        if (!$query_result = $this->_conn->query($sql))
		{
			$this->_conn->rollback();
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
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

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");
		
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

        $sql = "Select * from SiteUser where UserType='C' and login='" . $login . "';";
        if (!$query_result = $this->_conn->query($sql))
		{
			$this->_conn->rollback();
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
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

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");
		
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

        $sql = "Select * from SiteUser where UserType='C' and emailAddress='" . $email . "';";
        if (!$query_result = $this->_conn->query($sql))
		{
			$this->_conn->rollback();
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
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

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");
		
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

        $sql = "Select * from SiteUser where UserType='A' and id=" . $id . ";";
        if (!$query_result = $this->_conn->query($sql))
		{
			$this->_conn->rollback();
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
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

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");
		
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

        $sql = "Select * from SiteUser where UserType='A' and login='" . $login . "';";
        if (!$query_result = $this->_conn->query($sql))
		{
			$this->_conn->rollback();
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
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

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");
		
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

        $sql = "Select * from SiteUser where UserType='A' and login='" . $login . "';";
        if (!$query_result = $this->_conn->query($sql))
		{
			$this->_conn->rollback();
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
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

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");
		
		$this->_CloseConnection();	
		
		return $match;	
	}
	
	/*
		get all categories with associated products. use LIMIT.
	*/
	public function SelectAvailableCategoriesWithLimit( $firstItemIndex,  $numberOfItems)
	{
		$firstItemIndex = (integer) $firstItemIndex;
		$numberOfItems = (integer) $numberOfItems;
		
		if ($numberOfItems < 1 ) 
		{
			$numberOfItems = 1;
		}
		if ($firstItemIndex < 0 ) 
		{
			$firstItemIndex = 0;
		}
		
		$this->_OpenConnection();

        $sql = "SELECT c.id, c.name, cp.imageUrl FROM `cap` cp, `category` c WHERE cp.categoryId = c.id group by c.id "
                . "order by id, name LIMIT " . $firstItemIndex . ", " . $numberOfItems . ";";
        if (!$query_result = $this->_conn->query($sql))
		{
			$this->_conn->rollback();
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
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

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");
		
		$this->_CloseConnection();	
		
		return $available_categories;	
	}
	
	/*
		get a count of all categories associated with caps
	*/
	public function SelectCountOfAvailableCategories()
	{
		$this->_OpenConnection();

        $sql = "Select * from `category` WHERE `id` in (select distinct `categoryId` from `cap`);";
        if (!$query_result = $this->_conn->query($sql))
		{
			$this->_conn->rollback();
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		
		$categoryCount = $query_result->num_rows;
		
		if ($query_result)
		{
			$query_result->free();
		}

        Logging::Log('DataManager', 'query: ' . $sql  . "\r\n");
		
		$this->_CloseConnection();	
		
		return $categoryCount;	
	}
	
	
	/*
		get all products for a category, using the categoryId. use LIMIT.
	*/
	public function SelectCapsbyCategoryIdWithLimit( $categoryId,  $firstItemIndex,  $numberOfItems)
	{
		$firstItemIndex = (integer) $firstItemIndex;
		$numberOfItems = (integer) $numberOfItems;
		$categoryId = (integer) $categoryId;
		
		if ($numberOfItems < 1) 
		{
			$numberOfItems = 1;
		}
		if ($firstItemIndex < 0 ) 
		{
			$firstItemIndex = 0;
		}
		
		$this->_OpenConnection();

        $sql = "Select * from `cap` WHERE `categoryId` = " . $categoryId . " order by categoryId, id LIMIT "
            . $firstItemIndex . ", " . $numberOfItems . ";";
        if (!$query_result = $this->_conn->query($sql))
		{
			$this->_conn->rollback();
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
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

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");
		
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

        $sql = "Select * from `cap` WHERE `categoryId` = " . $categoryId . ";";
        if (!$query_result = $this->_conn->query($sql))
		{
			$this->_conn->rollback();
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		
		$capCount = $query_result->num_rows;
		
		if ($query_result)
		{
			$query_result->free();
		}

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");
		
		$this->_CloseConnection();
		
		return $capCount;	
	}
	
	/*
		get all caps associated with categories.
	*/
	public function SelectAllCapsWithCategoriesWithLimit($firstItemIndex, $numberOfItems)
	{
		$this->_OpenConnection();

        $sql = "Select * from `cap` where categoryId in (select distinct id from category) " .
            "order by id LIMIT " . $firstItemIndex . ", " . $numberOfItems . ";";
        if (!$query_result = $this->_conn->query($sql))
		{
			$this->_conn->rollback();
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
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

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");
		
		$this->_CloseConnection();	
		
		return $caps;	
	}
	
	/*
		get all products for a category, using the categoryId. use LIMIT.
	*/
	public function SelectCountOfAllCaps()
	{
		$this->_OpenConnection();

        $sql = "Select * from `cap`;";
        if (!$query_result = $this->_conn->query($sql))
		{
			$this->_conn->rollback();
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		
		$capcount = $query_result->num_rows;
		
		if ($query_result)
		{
			$query_result->free();
		}

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");
		
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

        $sql = "Select * from `cap` WHERE `id` = " . $capId . ";";
        if (!$query_result = $this->_conn->query($sql))
		{
			$this->_conn->rollback();
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
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

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");
		
		$this->_CloseConnection();	
		
		return $cap;	
	}
	
	/*
		get all orders and orderitems for a customer. use LIMIT.
	*/
	public function SelectOrdersWithItemsByCustomer( $customerId,  $firstItemIndex,  $numberOfItems)
	{
		$firstItemIndex = (integer) $firstItemIndex;
		$numberOfItems = (integer) $numberOfItems;
		$customerId = (integer) $customerId;
		
		$this->_OpenConnection();

        $sql = "Select id, userId, status, datePlaced, capId, quantity from `CustomerOrder` co JOIN `OrderItem`" .
            " oi ON oi.`OrderId`=co.`id` WHERE userId=" . $customerId . " order by status, datePlaced, capId, quantity limit " .
            $firstItemIndex . ", " . $numberOfItems . ";";
        if (!$query_result = $this->_conn->query($sql))
		{
			$this->_conn->rollback();
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
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

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");
		
		$this->_CloseConnection();	
		
		return $orders;	
	}
	
	/*
		get all orders and orderitems for a customer. use LIMIT.
	*/
	public function SelectOrderSummariesByCustomer( $customerId,  $firstItemIndex,  $numberOfItems)
	{
		$firstItemIndex = (integer) $firstItemIndex;
		$numberOfItems = (integer) $numberOfItems;
		$customerId = (integer) $customerId;
		
		$this->_OpenConnection();

        $sql = "SELECT co.id as id, co.status as status, co.datePlaced as datePlaced, " .
            " sum(oi.quantity) as totalQuantity, sum(oi.quantity * c.price) as totalPrice FROM `orderitem` oi, `customerorder` co, " .
            " `cap` c WHERE userId=" . $customerId . " and oi.orderid = co.id AND c.id = oi.capId group by orderId " .
            " order by co.status, co.datePlaced, co.id limit " . $firstItemIndex . ", " . $numberOfItems . ";";
        if (!$query_result = $this->_conn->query($sql))
		{
			$this->_conn->rollback();
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		
		$summaries = array();
		
		if ($query_result->num_rows > 0)
		{
			while ($row = $query_result->fetch_assoc())
			{
                $summaries[] = $row;
			}
		}
		
		if ($query_result)
		{
			$query_result->free();
		}

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");
		
		$this->_CloseConnection();	
		
		return $summaries;
	}
	
	/*
		get count of order summaries
	*/
	public function GetCountOfOrderSummariesByCustomer($customerId)
	{
		$customerId = (integer) $customerId;
		
		$this->_OpenConnection();

        $sql = "SELECT co.id as id, " .
            " sum(oi.quantity) as totalQuantity, sum(oi.quantity * c.price) as totalPrice FROM `orderitem` oi, `customerorder` co, " .
            " `cap` c WHERE userId=" . $customerId . " and oi.orderid = co.id AND c.id = oi.capId group by orderId ;";
        if (!$query_result = $this->_conn->query($sql))
		{
			$this->_conn->rollback();
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
			header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
			exit;
		}
		
		$summaryCount = $query_result->num_rows;
		
		if ($query_result)
		{
			$query_result->free();
		}

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");
		
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

        $sql = "Select 1 FROM `SiteUser` WHERE passwordsalt='" . $salt . "';";
        if (!$query_result = $this->_conn->query($sql))
		{
			$this->_conn->rollback();
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
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

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");
		
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

        $sql = "Select passwordsalt, passwordhash FROM `SiteUser` where UserType='A' and login='" . $login . "';";
        if (!$query_result = $this->_conn->query($sql))
		{
			$this->_conn->rollback();
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
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

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");
		
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

        $sql = "Select * FROM `siteuser` WHERE userType='C' and login='" . $login . "';";
        if (!$query_result = $this->_conn->query($sql))
		{
			$this->_conn->rollback();
            $this->_CloseConnection();
			$_SESSION["last_Error"] = "DB_Error_Generic";
			$_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
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

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");
		
		$this->_CloseConnection();	
		
		return $data;
	}

    /*
		generate a new category
	*/
    public function InsertCategory($categoryName)
    {
        $this->_OpenConnection();

        // sanitize inputs
        $categoryName = $this->_conn->real_escape_string($categoryName);

        $this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

        $sql =  "insert into Category (name) values ('" . $categoryName . "');";
        $this->_conn->query($sql);

        if (!$this->_conn->commit())
        {
            $this->_conn->rollback();
            $this->_CloseConnection();
            $_SESSION["last_Error"] = "DB_Error_Generic";
            $_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
            header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
            exit;
        }

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");

        $this->_CloseConnection();
    }

    /*
		delete an existing category
	*/
    public function DeleteCategory($id)
    {
        $this->_OpenConnection();

        // sanitize inputs
        $id = (integer) ($id);

        $this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

        // cannot delete caps currently in use.
        $sql =  "delete from Category where id = " . $id . " AND id NOT IN " .
            " (select distinct categoryId from cap);";
        $this->_conn->query($sql);

        if (!$this->_conn->commit())
        {
            $this->_conn->rollback();
            $this->_CloseConnection();
            $_SESSION["last_Error"] = "DB_Error_Generic";
            $_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
            header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
            exit;
        }

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");

        $this->_CloseConnection();
    }

    /*
		generate a new supplier
	*/
    public function InsertSupplier($supplierName, $emailAddress, $homeNumber, $workNumber, $mobileNumber)
    {
        $this->_OpenConnection();

        // sanitize inputs
        $supplierName = $this->_conn->real_escape_string($supplierName);
        $emailAddress = $this->_conn->real_escape_string($emailAddress);
        $homeNumber = $this->_conn->real_escape_string($homeNumber);
        $workNumber = $this->_conn->real_escape_string($workNumber);
        $mobileNumber = $this->_conn->real_escape_string($mobileNumber);

        $this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
        $sql =  "insert into Supplier (name, emailAddress, homeNumber, workNumber, mobileNumber) values ".
            "('" . $supplierName . "','" . $emailAddress . "','" . $homeNumber .
            "','" . $workNumber . "','" . $mobileNumber . "');";
        $this->_conn->query($sql);

        if (!$this->_conn->commit())
        {
            $this->_conn->rollback();
            $this->_CloseConnection();
            $_SESSION["last_Error"] = "DB_Error_Generic";
            $_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
            header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
            exit;
        }

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");

        $this->_CloseConnection();
    }

    /*
		delete an existing supplier
	*/
    public function DeleteSupplier($id)
    {
        $this->_OpenConnection();

        // sanitize inputs
        $id = (integer) ($id);

        $this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

        // cannot delete suppliers currently in use.
        $sql =  "delete from Supplier where id = " . $id . " AND id NOT IN " .
            " (select distinct supplierId from cap);";
        $this->_conn->query($sql);

        if (!$this->_conn->commit())
        {
            $this->_conn->rollback();
            $this->_CloseConnection();
            $_SESSION["last_Error"] = "DB_Error_Generic";
            $_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
            header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
            exit;
        }

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");

        $this->_CloseConnection();
    }

    /*
		generate a new cap
	*/
    public function InsertCap($capName, $price, $description, $imageUrl, $supplierId, $categoryId)
    {
        $this->_OpenConnection();

        // sanitize inputs
        $capName = $this->_conn->real_escape_string($capName);
        $price = (float) ($price);
        $description = $this->_conn->real_escape_string($description);
        $imageUrl = $this->_conn->real_escape_string($imageUrl);
        $supplierId = (integer) ($supplierId);
        $categoryId = (integer) ($categoryId);

        $this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
        $sql =  "insert into Cap (name, price, description, imageUrl, supplierId, categoryId) values ".
            "('" . $capName . "'," . $price . ",'" . $description .
            "','" . $imageUrl . "'," . $supplierId . "," . $categoryId . ");";
        $this->_conn->query($sql);

        if (!$this->_conn->commit())
        {
            $this->_conn->rollback();
            $this->_CloseConnection();
            $_SESSION["last_Error"] = "DB_Error_Generic";
            $_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
            header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
            exit;
        }

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");

        $this->_CloseConnection();
    }

    /*
		delete an existing cap
	*/
    public function DeleteCap($id)
    {
        $this->_OpenConnection();

        // sanitize inputs
        $id = (integer) ($id);

        $this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

        // cannot delete caps currently used by Orders.
        $sql =  "delete from Cap where id = " . $id . " AND id NOT IN " .
            " (select distinct capId from orderitem);";
        $this->_conn->query($sql);

        if (!$this->_conn->commit())
        {
            $this->_conn->rollback();
            $this->_CloseConnection();
            $_SESSION["last_Error"] = "DB_Error_Generic";
            $_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
            header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
            exit;
        }

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");

        $this->_CloseConnection();
    }

    /*
		disable an existing customer
	*/
    public function DisableCustomer($id)
    {
        $this->_OpenConnection();

        // sanitize inputs
        $id = (integer) ($id);

        $this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

        // cannot delete caps currently used by Orders.
        $sql =  "update SiteUser set idDisabled = 1 where id = " . $id . ";";
        $this->_conn->query($sql);

        if (!$this->_conn->commit())
        {
            $this->_conn->rollback();
            $this->_CloseConnection();
            $_SESSION["last_Error"] = "DB_Error_Generic";
            $_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
            header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
            exit;
        }

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");

        $this->_CloseConnection();
    }

    /*
		delete an existing user
	*/
    public function DeleteUser($id)
    {
        $this->_OpenConnection();

        // sanitize inputs
        $id = (integer) ($id);

        $this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

        // cannot delete customers with orders..
        $sql =  "delete from SiteUser where id = " . $id . " AND id not in " .
            "(select distinct userId from customerOrder);";
        $this->_conn->query($sql);

        if (!$this->_conn->commit())
        {
            $this->_conn->rollback();
            $this->_CloseConnection();
            $_SESSION["last_Error"] = "DB_Error_Generic";
            $_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
            header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
            exit;
        }

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");

        $this->_CloseConnection();
    }

    /*
     *   Retire a Cap, so customers cannot order it.
     */
    public function RetireCap($id)
    {
        $this->_OpenConnection();

        // sanitize inputs
        $id = (integer) ($id);

        $this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

        // NULL categoryId represents a retired cap.
        $sql =  "update Cap set `categoryId` = NULL where id = " . $id . ";";
        $this->_conn->query($sql);

        if (!$this->_conn->commit())
        {
            $this->_conn->rollback();
            $this->_CloseConnection();
            $_SESSION["last_Error"] = "DB_Error_Generic";
            $_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
            header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
            exit;
        }

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");

        $this->_CloseConnection();
    }

    /*
		delete an existing order
	*/
    public function DeleteOrder($id)
    {
        $this->_OpenConnection();

        // sanitize inputs
        $id = (integer) ($id);

        $this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

        // cannot delete suppliers currently in use.
        $sql =  "delete from orderItem where orderId = " . $id . " ;";
        $this->_conn->query($sql);

        if (!$this->_conn->commit())
        {
            $this->_conn->rollback();
            $this->_CloseConnection();
            $_SESSION["last_Error"] = "DB_Error_Generic";
            $_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
            header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
            exit;
        }

        $this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");

        $sql =  "delete from customerOrder where id = " . $id . " ;";
        $this->_conn->query($sql);

        if (!$this->_conn->commit())
        {
            $this->_conn->rollback();
            $this->_CloseConnection();
            $_SESSION["last_Error"] = "DB_Error_Generic";
            $_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
            header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
            exit;
        }

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");

        $this->_CloseConnection();
    }

    /*
     *   RChange order status
     */
    public function ChangeOrderStatus($id, $status)
    {
        $this->_OpenConnection();

        // sanitize inputs
        $status = $this->_conn->real_escape_string($status);
        $status = strtolower($status);

        if (!in_array($status))
        {
            $this->_CloseConnection();
            $_SESSION["last_Error"] = "DB_Error_Generic";
            $_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; bad order status";
            header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
            exit;
        }

        $this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

        // change status
        $sql =  "update CustomerOrder set `status` = '" . $status . "' where id = " . $id . ";";
        $this->_conn->query($sql);

        if (!$this->_conn->commit())
        {
            $this->_conn->rollback();
            $this->_CloseConnection();
            $_SESSION["last_Error"] = "DB_Error_Generic";
            $_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
            header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
            exit;
        }

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");

        $this->_CloseConnection();
    }

    /*
		get all categories
	*/
    public function SelectAllCategories()
    {
        $this->_OpenConnection();

        $sql = "Select * from `category` order by name;";
        if (!$query_result = $this->_conn->query($sql))
        {
            $this->_conn->rollback();
            $this->_CloseConnection();
            $_SESSION["last_Error"] = "DB_Error_Generic";
            $_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
            header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
            exit;
        }

        $items = array();

        if ($query_result->num_rows > 0)
        {
            while ($row = $query_result->fetch_assoc())
            {
                $items[] = $row;
            }
        }

        if ($query_result)
        {
            $query_result->free();
        }

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");

        $this->_CloseConnection();

        return $items;
    }

    /*
		get all suppliers
	*/
    public function SelectAllSuppliers()
    {
        $this->_OpenConnection();

        $sql = "Select * from `supplier` order by name;";
        if (!$query_result = $this->_conn->query($sql))
        {
            $this->_conn->rollback();
            $this->_CloseConnection();
            $_SESSION["last_Error"] = "DB_Error_Generic";
            $_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
            header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
            exit;
        }

        $items = array();

        if ($query_result->num_rows > 0)
        {
            while ($row = $query_result->fetch_assoc())
            {
                $items[] = $row;
            }
        }

        if ($query_result)
        {
            $query_result->free();
        }

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");

        $this->_CloseConnection();

        return $items;
    }

    /*
		get all orders
	*/
    public function SelectAllOrders()
    {
        $this->_OpenConnection();

        $sql = "Select co.id as id, su.userId as userId, su.firstName as firstName, su.lastName as lastName, " .
            " co.datePlaced as datePlaced, co.status as status from `customerOrder` co, `siteUser` su ".
            "where co.userId = su.id order by datePlaced;";
        if (!$query_result = $this->_conn->query($sql))
        {
            $this->_conn->rollback();
            $this->_CloseConnection();
            $_SESSION["last_Error"] = "DB_Error_Generic";
            $_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
            header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
            exit;
        }

        $items = array();

        if ($query_result->num_rows > 0)
        {
            while ($row = $query_result->fetch_assoc())
            {
                $items[] = $row;
            }
        }

        if ($query_result)
        {
            $query_result->free();
        }

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");

        $this->_CloseConnection();

        return $items;
    }

    /*
		get all users
	*/
    public function SelectAllSiteUsers()
    {
        $this->_OpenConnection();

        $sql = "Select * from siteUser order by id;";
        if (!$query_result = $this->_conn->query($sql))
        {
            $this->_conn->rollback();
            $this->_CloseConnection();
            $_SESSION["last_Error"] = "DB_Error_Generic";
            $_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
            header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
            exit;
        }

        $items = array();

        if ($query_result->num_rows > 0)
        {
            while ($row = $query_result->fetch_assoc())
            {
                $items[] = $row;
            }
        }

        if ($query_result)
        {
            $query_result->free();
        }

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");

        $this->_CloseConnection();

        return $items;
    }

    /*
     get all caps
    */
    public function SelectAllCaps()
    {
        $this->_OpenConnection();

        $sql = "Select * from cap order by id;";
        if (!$query_result = $this->_conn->query($sql))
        {
            $this->_conn->rollback();
            $this->_CloseConnection();
            $_SESSION["last_Error"] = "DB_Error_Generic";
            $_SESSION["Error_MSG"] = (string) $this->_conn->errno . "; " . $this->_conn->error . "; SQL=". $sql;
            header("Location: http://dochyper.unitec.ac.nz/AskewR04/PHP_Assignment/Pages/Error/DB_Error_SQL.php");
            exit;
        }

        $items = array();

        if ($query_result->num_rows > 0)
        {
            while ($row = $query_result->fetch_assoc())
            {
                $items[] = $row;
            }
        }

        if ($query_result)
        {
            $query_result->free();
        }

        Logging::Log('DataManager, query: ' . $sql  . "\r\n");

        $this->_CloseConnection();

        return $items;
    }
}