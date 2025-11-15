<?php
	session_start();
	require("../includes/config.php");
	$currentDate = date('Y-m-d');
	
	// Check for proper session and retailer login
	if (!isset($_SESSION['retailer_login']) || $_SESSION['retailer_login'] !== true || !isset($_SESSION['retailer_id'])) {
		header('Location: ../index.php');
		exit;
	}
	
	$retailer_id = $_SESSION['retailer_id'];
	
	// Get total number of products for processing
	$sql = "SELECT MAX(pro_id) as max_id FROM products";
	$result = mysqli_query($con,$sql);
	
	if (!$result) {
		die("Database error: " . mysqli_error($con));
	}
	
	$row = mysqli_fetch_array($result);
	$total_products = $row['max_id'];
	
	$quantityArray = array();
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		if(!empty($_POST['total_price']) && $_POST['total_price'] > 0){
			$total_price = floatval($_POST['total_price']);
			
			// Collect quantities for each product
			for($i=1;$i<=$total_products;$i++){
				if(isset($_POST['txtQuantity'.$i]) && !empty($_POST['txtQuantity'.$i])) {
					$quantityArray[$i] = floatval($_POST['txtQuantity'.$i]);
				}
			}
			
			// Check if we have at least one product with quantity
			if(empty($quantityArray)) {
				header('Location: order_items.php?error=no_items');
				exit;
			}
		}
		else{
			header('Location: order_items.php?error=invalid_total');
			exit;
		}
	} else {
		header('Location: order_items.php');
		exit;
	}
	
	// Insert the order
	$query_insertOrder = "INSERT INTO orders(date,retailer_id,total_amount,approved,status) VALUES('$currentDate','$retailer_id','$total_price',0,0)";
	
	if($con->query($query_insertOrder) === true){
		// Get the inserted order ID
		$order_id = mysqli_insert_id($con);
		
		// Insert order items with proper price and total calculation
		foreach($quantityArray as $key_productId => $value_quantity){
			if($value_quantity > 0){
				// Get product price
				$price_query = "SELECT pro_price FROM products WHERE pro_id = '$key_productId'";
				$price_result = mysqli_query($con, $price_query);
				
				if($price_result && mysqli_num_rows($price_result) > 0) {
					$price_row = mysqli_fetch_array($price_result);
					$unit_price = $price_row['pro_price'];
					$total_item_price = $unit_price * $value_quantity;
					
					$query_insertOrderItems = "INSERT INTO order_items(order_id,pro_id,quantity,price,total) VALUES('$order_id','$key_productId','$value_quantity','$unit_price','$total_item_price')";
					
					if(!mysqli_query($con,$query_insertOrderItems)) {
						// If insertion fails, log the error but continue
						error_log("Failed to insert order item for product $key_productId: " . mysqli_error($con));
					}
				}
			}
		}
		
		header('Location:view_my_orders.php?status=success');
	}
	else{
		error_log("Failed to insert order: " . mysqli_error($con));
		header('Location:order_items.php?error=database_error');
	}
?>