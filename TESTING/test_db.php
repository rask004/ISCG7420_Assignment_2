<?php  
	include_once( '../Includes/DataLayer.php');  
	
	ini_set('display_errors', '1');
?>
	
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quality Caps -
        <?php
			if (isset($_SESSION['IsAuthenticated']) && $_SESSION['IsAuthenticated'] == 1)
			{
				echo 'Profile';
			}
			else
			{
				echo 'Register';
			}
        ?>
    </title>
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.structure.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui.theme.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/Common.css">
    <script type="text/javascript" src="../js/jquery.js"></script>
</head>
<body>
	<div style="overflow-x:scroll">

	<?php $dataManager = new \DataLayer\DataManager; ?>
    
    <H4>Select Single Customer, ID: 2</H4>
    
    <br/>
    
    <?php
		$customer = $dataManager->selectSingleCustomer(2);
		
		echo '<div class="container-fluid">'.
		'<table>'.
		'<tr>';
		foreach (array_keys($customer) as $key)
		{
			echo '<th style="border: solid black 1px">' . $key . '</th>';
		}
		echo '</tr>';
		echo '<tr>';
		foreach (array_values($customer) as $value)
		{
			echo '<td style="border: solid black 1px">' . $value . '</td>';
		}
		echo '</tr>' .
		'</table>'.
		'</div>'
	?>
    
    <br/>
    
    <H4>Select Single Customer, Login = 'test_customer'</H4>
    
    <?php
		$customer = $dataManager->selectSingleCustomerByLogin("test_customer");
		
		echo '<div class="container-fluid">'.
		'<table>'.
		'<tr>';
		foreach (array_keys($customer) as $key)
		{
			echo '<th style="border: solid black 1px">' . $key . '</th>';
		}
		echo '</tr>';
		echo '<tr>';
		foreach (array_values($customer) as $value)
		{
			echo '<td style="border: solid black 1px">' . $value . '</td>';
		}
		echo '</tr>' .
		'</table>'.
		'</div>'
	?>
    
    <br/>
   
    <H4>Match Logins</H4>
    
    <?php
		$matches = array();
		$test_values = array("fake_user", "test_customer", 7, true);
		foreach ($test_values as $value)
		{
			$matches[] = $dataManager->matchCustomerByLogin($value);
		}
		
		echo '<div class="container-fluid">'.
		'<table>'.
		'<tr>';
		foreach ($test_values as $key => $value)
		{
			echo '<th style="border: solid black 1px">' . $value . '</th>';
		}
		echo '</tr>';
		echo '<tr>';
		foreach (array_values($matches) as $value)
		{
			echo '<td style="border: solid black 1px">' . $value . '</td>';
		}
		echo '</tr>' .
		'</table>'.
		'</div>'
	?>
    
    <br/>
    
    <H4>Match Emails</H4>
    
    <?php
		$matches = array();
		$test_values = array("rolandjamesaskew37@gmail.com", "'; delete from blahblah; -- ", "AskewR04@myunitec.ac.nz", 7, true);
		foreach ($test_values as $value)
		{
			$matches[] = $dataManager->matchCustomerByEmail($value);
		}
		
		echo '<div class="container-fluid">'.
		'<table>'.
		'<tr>';
		foreach (array_values($test_values) as $value)
		{
			echo '<th style="border: solid black 1px">' . $value . '</th>';
		}
		echo '</tr>';
		echo '<tr>';
		foreach (array_values($matches) as $value)
		{
			echo '<td style="border: solid black 1px">' . $value . '</td>';
		}
		echo '</tr>' .
		'</table>'.
		'</div>';
	?>
    
    <H4>Select Single Admin, ID = 1</H4>
    
    <?php
		$admin = $dataManager->selectSingleAdmin(2);
		
		echo '<div class="container-fluid">'.
		'<table>'.
		'<tr>';
		foreach (array_keys($admin) as $key)
		{
			echo '<th style="border: solid black 1px">' . $key . '</th>';
		}
		echo '</tr>';
		echo '<tr>';
		foreach (array_values($admin) as $value)
		{
			echo '<td style="border: solid black 1px">' . $value . '</td>';
		}
		echo '</tr>' .
		'</table>'.
		'</div>'
	?>
    
    <br/>
    
    <H4>Select Single Admin, Login = 'test_admin'</H4>
    
    <?php
		$admin = $dataManager->selectSingleAdminByLogin('test_admin');
		
		echo '<div class="container-fluid">'.
		'<table>'.
		'<tr>';
		foreach (array_keys($admin) as $key)
		{
			echo '<th style="border: solid black 1px">' . $key . '</th>';
		}
		echo '</tr>';
		echo '<tr>';
		foreach (array_values($admin) as $value)
		{
			echo '<td style="border: solid black 1px">' . $value . '</td>';
		}
		echo '</tr>' .
		'</table>'.
		'</div>'
	?>
    
    <br/>
   
    <H4>Match Admin Logins</H4>
    
    <?php
		$matches = array();
		$test_values = array("fake_admin", "test_admin", 7, true);
		foreach ($test_values as $value)
		{
			$matches[] = $dataManager->matchAdminByLogin($value);
		}
		
		echo '<div class="container-fluid">'.
		'<table>'.
		'<tr>';
		foreach (array_values($test_values) as $value)
		{
			echo '<th style="border: solid black 1px">' . $value . '</th>';
		}
		echo '</tr>';
		echo '<tr>';
		foreach (array_values($matches) as $value)
		{
			echo '<td style="border: solid black 1px">' . $value . '</td>';
		}
		echo '</tr>' .
		'</table>'.
		'</div>'
	?>
    
    <br/>
    
    <H4>Get categories, only those associated to products, using LIMIT</H4>
    
    <?php
		$categories = $dataManager->selectAvailableCategoriesWithLimit(0, 20000);
		$headers = array();
		foreach (array_keys($categories[0]) as $key)
		{
			$headers[] = $key;
		}
		
		echo '<div class="container-fluid">'.
		'<table>'.
		'<tr><th style="border: solid black 1px">start</th><th style="border: solid black 1px">length</th>';
		foreach (array_values($headers) as $value)
		{
			echo '<th style="border: solid black 1px">' . $value . '</th>';
		}
		echo '</tr>';
		
		foreach($categories as $category)
		{
			echo '<tr><td style="border: solid black 1px">0</td><td style="border: solid black 1px">20000</td>';
			foreach (array_values($category) as $value)
			{
				echo '<td style="border: solid black 1px">' . $value . '</td>';
			}
			echo '</tr>';
		}
		
		echo '<tr><td></td></tr>';
		
		$categories = $dataManager->selectAvailableCategoriesWithLimit(0, 1);
		foreach($categories as $category)
		{
			echo '<tr><td style="border: solid black 1px">0</td><td style="border: solid black 1px">1</td>';
			foreach (array_values($category) as $value)
			{
				echo '<td style="border: solid black 1px">' . $value . '</td>';
			}
			echo '</tr>';
		}
		
		echo '<tr><td></td></tr>';
		
		$categories = $dataManager->selectAvailableCategoriesWithLimit(1, 2);
		foreach($categories as $category)
		{
			echo '<tr><td style="border: solid black 1px">1</td><td style="border: solid black 1px">2</td>';
			foreach (array_values($category) as $value)
			{
				echo '<td style="border: solid black 1px">' . $value . '</td>';
			}
			echo '</tr>';
		}
		
		echo '</table>'.
		'</div>';
	?>
    
    <br/>
    
    <H4>Get caps, only those associated to a given category, using LIMIT</H4>
    
    <?php
		$caps = $dataManager->selectCapsbyCategoryIdWithLimit(1, 0, 20000);
		$headers = array();
		foreach (array_keys($caps[0]) as $key)
		{
			$headers[] = $key;
		}
		
		echo '<div class="container-fluid">'.
		'<table>'.
		'<tr><th style="border: solid black 1px">categoryId</th><th style="border: solid black 1px">start</th><th style="border: solid black 1px">length</th>';
		foreach (array_values($headers) as $value)
		{
			echo '<th style="border: solid black 1px">' . $value . '</th>';
		}
		echo '</tr>';
		
		foreach($caps as $cap)
		{
			echo '<tr><td style="border: solid black 1px">1</td><td style="border: solid black 1px">0</td><td style="border: solid black 1px">20000</td>';
			foreach (array_values($cap) as $value)
			{
				echo '<td style="border: solid black 1px">' . $value . '</td>';
			}
			echo '</tr>';
		}
		
		echo '<tr><td></td></tr>';
		
		$caps = $dataManager->selectCapsbyCategoryIdWithLimit(2, 0,3);
		foreach($caps as $cap)
		{
			echo '<tr><td style="border: solid black 1px">2</td><td style="border: solid black 1px">0</td><td style="border: solid black 1px">3</td>';
			foreach (array_values($cap) as $value)
			{
				echo '<td style="border: solid black 1px">' . $value . '</td>';
			}
			echo '</tr>';
		}
		
		echo '<tr><td></td></tr>';
		
		$caps = $dataManager->selectCapsbyCategoryIdWithLimit(2, 3, 5);
		foreach($caps as $cap)
		{
			echo '<tr><td style="border: solid black 1px">2</td><td style="border: solid black 1px">3</td><td style="border: solid black 1px">5</td>';
			foreach (array_values($cap) as $value)
			{
				echo '<td style="border: solid black 1px">' . $value . '</td>';
			}
			echo '</tr>';
		}
		
		echo '<tr><td></td></tr>';
		
		$caps = $dataManager->selectCapsbyCategoryIdWithLimit(3, 0, 99);
		foreach($caps as $cap)
		{
			echo '<tr><td style="border: solid black 1px">3</td><td style="border: solid black 1px">0</td><td style="border: solid black 1px">99</td>';
			foreach (array_values($cap) as $value)
			{
				echo '<td style="border: solid black 1px">' . $value . '</td>';
			}
			echo '</tr>';
		}
		
		echo '<tr><td></td></tr>';
		
		echo '</table>'.
		'</div>';
	?>
    
    <br/>
    
    
    <H4>Select Single Cap, id = 2</H4>
    
    <?php
		$cap = $dataManager->selectSingleCap(2);
		
		echo '<div class="container-fluid">'.
		'<table>'.
		'<tr>';
		foreach (array_keys($cap) as $key)
		{
			echo '<th style="border: solid black 1px">' . $key . '</th>';
		}
		echo '</tr>';
		echo '<tr>';
		foreach (array_values($cap) as $value)
		{
			echo '<td style="border: solid black 1px">' . $value . '</td>';
		}
		echo '</tr>' .
		'</table>'.
		'</div>'
	?>
    
    <br/>
    
    
    <H4>Select Orders, customer id = 2</H4>
    
    <?php
		$orderitems = $dataManager->selectOrdersWithItemsByCustomer(2, 0, 2000);
		$headers = array();
		if (count($orderitems) == 0)
		{
			echo '<div class="container-fluid"><p>There are no order items to show.</p></div>';
		}
		else
		{
			foreach (array_keys($orderitems[0]) as $key)
			{
				$headers[] = $key;
			}
			
			echo '<div class="container-fluid">'.
				'<table>';
				
			echo '<tr>';
			foreach (array_values($headers) as $key)
			{
				echo '<th style="border: solid black 1px">' . $key . '</th>';
			}
			echo '</tr>';
				
			foreach($orderitems as $item)
			{
				echo '<tr>';
				foreach (array_values($item) as $value)
				{
					echo '<td style="border: solid black 1px">' . $value . '</td>';
				}
				echo '</tr>';
			}
			
			echo '</table></div>';
		}
		
	?>
    
    <br/>
    
    </div>

</body>
</html>