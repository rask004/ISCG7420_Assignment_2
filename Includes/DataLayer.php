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
		//$this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
		
		if(!$this->_conn->query("create table if not exists `SiteUser`(`id` int UNSIGNED AUTO_INCREMENT primary key, `login`   varchar(64)    not null, " .
                            "`passwordhash`    varchar(64)    not null, `userType`    char(1)     not null, `emailAddress`    varchar(100)   not null, " .
                            "`homeNumber`  varchar(11), `workNumber`  varchar(11), `mobileNumber`    varchar(14), `firstName`   varchar(32), " .
                            "`lastName`    varchar(32), `streetAddress`   varchar(64), `suburb`      varchar(24), `city`        varchar(16), " .
                            "`isDisabled`  BIT(1)    not null DEFAULT 0 );"))
		{
			echo 'Siteuser Creation failed';
			echo ($this->_conn->errno . "; " . $this->_conn->error);
		}
		

        if(!$this->_conn->query("create table if not exists CustomerOrder(`id` int UNSIGNED AUTO_INCREMENT primary key, `userId`  int not null, " .
                           "`status` varchar(7) not null DEFAULT 'waiting', `datePlaced` datetime not null DEFAULT NOW()".
						   "Foreign Key (`userId`) References `SiteUser`(`id`));"))
		{
			echo 'CustomerOrder Creation failed';
			echo ($this->_conn->errno . "; " . $this->_conn->error);
		}
		

        if(!$this->_conn->query("create table if not exists `Supplier`(`id` int UNSIGNED AUTO_INCREMENT primary key, `name`    varchar(32)    not null, " .
                            "`homeNumber`   varchar(11)    null, `worknumber`   varchar(11)    null, " .
                            "`mobileNumber` varchar(13)    null, `emailAddress`    varchar(64)    not null);"))
		{
			echo 'Supplier Creation failed';
			echo ($this->_conn->errno . "; " . $this->_conn->error);
		}
		

        if(!$this->_conn->query("create table if not exists `Category`(`id` int UNSIGNED AUTO_INCREMENT primary key, `name`    varchar(40)    not null); "))
		{
			echo 'Category Creation failed';
			echo ($this->_conn->errno . "; " . $this->_conn->error);
		}
		

        if(!$this->_conn->query("create table if not exists `Cap`(`id` int UNSIGNED AUTO_INCREMENT primary key, `name`    varchar(40)    not null, " .
                            "`price`    real UNSIGNED    not null, `description` varchar(512)   not null, `imageUrl` varchar(96) not null, " .
                            "`supplierId` int UNSIGNED     not null,    " .
                            "`categoryId`  int UNSIGNED     not null,    " . "CONSTRAINT fk_supplier Foreign Key (`supplierId`) References  `Supplier`(`id`), CONSTRAINT fk_category Foreign Key (`categoryId`) References `Category`(`id`)); "))
		{
			echo 'Cap Creation failed';
			echo ($this->_conn->errno . "; " . $this->_conn->error);
		}
		

        if(!$this->_conn->query("create table if not exists `OrderItem`(`orderId`     int UNSIGNED      not null , " .
                                                       "`capId`       int UNSIGNED     not null, " .
                                                       "`quantity`    smallint UNSIGNED    not null, " .
                                                       "Constraint  orderItem_pk    Primary Key(`capId`, `orderId`),".
													   "CONSTRAINT fk_OrderOrderItem Foreign Key (`orderId`) References `CustomerOrder`(`id`)". 
													   "CONSTRAINT fk_capOrderItem Foreign Key (`capId`) References `Cap`(`id`)); "))
		{
			echo 'OrderItem Creation failed';
			echo ($this->_conn->errno . "; " . $this->_conn->error);
		}
		
		/*
		if (!$this->_conn->commit())
		{
			echo 'Table construction failed.<br/>';
		}
		else 
		{
			$results = $this->_conn->query("SHOW TABLES;");
			$rows = $results->fetch_all();
			$results->free();
			
			foreach ($rows as $row) 
			{
				print_r($row);	
			}
		}
		*/
														  
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
			
			// need to know the orderid. request a new order id first.
			$result = $this->_conn->query("SHOW TABLE STATUS LIKE 'CustomerOrder'");
			$data = $this->_conn->fetch_assoc($result);
			$next_order_id = $data['auto_increment'];
			
			$this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
			// create the customer order.
			$sql = "insert into CustomerOrder (id, userId) values (".$next_order_id.",".$customer_id.");";
			$this->_conn->query($sql);

			// create each new order item, using the order id.
			$sql = "insert into OrderItem (orderId, capId, quantity) values ";			
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
		$this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
		
		$sql =  "insert into SiteUser (firstName, lastName, login, passwordhash, email, homenumber, worknumber, mobilenumber," .
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
		$this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
		
		$sql =  "update SiteUser set firstName='".$first_name."', set lastName='".$last_name."', set login='".$login."'," .
		" set email='".$email."', set homenumber='".$home_number."', set worknumber='".$work_number."', set mobilenumber='".$mobile_number."',". 
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
		$this->_conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
		
		$sql =  "update SiteUser set passwordhash='".$hash."' where id=" . $id . ";";
		$this->_conn->query($sql);
		
		if (!$this->_conn->commit())
		{
			//TODO: should stop, rollback and redirect to error page if an error occurs.
		}
		
		$this->_closeConnection();								
	}
	
	//TODO: do retrieval functions.	
}