<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require("../includes/config.php");
	session_start();
	if(isset($_SESSION['manufacturer_login'])) {
		if($_SESSION['manufacturer_login'] == true) {
			$manufacturer_id = $_SESSION['manufacturer_id'];
			
			// Validate order ID
			if(!isset($_GET['id']) || empty($_GET['id'])) {
				echo "<script> alert(\"Invalid order ID\"); </script>";
				echo "<script> window.location.href='view_orders.php'; </script>";
				exit();
			}
			
			$id = mysqli_real_escape_string($con, $_GET['id']);
			
			// Check if order exists and is not already approved
			$checkOrderQuery = "SELECT * FROM orders WHERE order_id='$id' AND approved=0";
			$checkOrderResult = mysqli_query($con, $checkOrderQuery);
			
			if(mysqli_num_rows($checkOrderResult) == 0) {
				echo "<script> alert(\"Order not found or already confirmed\"); </script>";
				echo "<script> window.location.href='view_orders.php'; </script>";
				exit();
			}
			
			// Get ordered items
			$queryOrderItems = "SELECT pro_id, quantity FROM order_items WHERE order_id='$id'";
			$resultOrderItems = mysqli_query($con, $queryOrderItems);
			
			// Check stock availability and prepare updates
			$canConfirm = true;
			$insufficientProducts = array();
			$stockUpdates = array();
			
			// Debug log
			error_log("=== Order Confirmation Debug ===");
			error_log("Manufacturer ID: " . $manufacturer_id);
			error_log("Order ID: " . $id);
			
			while($orderItem = mysqli_fetch_array($resultOrderItems)) {
				$pro_id = $orderItem['pro_id'];
				$ordered_qty = $orderItem['quantity'];
				
				error_log("Checking product ID: $pro_id, ordered quantity: $ordered_qty");
				
				// Get manufacturer's current stock for this product
				$queryStock = "SELECT quantity FROM manufacturer_stock 
							   WHERE manufacturer_id='$manufacturer_id' AND product_id='$pro_id'";
				$resultStock = mysqli_query($con, $queryStock);
				
				error_log("Stock query: " . $queryStock);
				error_log("Rows returned: " . mysqli_num_rows($resultStock));
				
				if(mysqli_num_rows($resultStock) > 0) {
					$stockRow = mysqli_fetch_array($resultStock);
					$available_qty = $stockRow['quantity'];
					
					error_log("Available quantity: $available_qty");
					
					if($available_qty >= $ordered_qty) {
						// Sufficient stock - prepare to update
						$new_qty = $available_qty - $ordered_qty;
						$stockUpdates[] = array(
							'pro_id' => $pro_id, 
							'new_quantity' => $new_qty
						);
						error_log("Stock sufficient. Will update to: $new_qty");
					} else {
						// Insufficient stock
						$canConfirm = false;
						error_log("INSUFFICIENT STOCK! Need: $ordered_qty, Have: $available_qty");
						
						// Get product name for error message
						$queryProd = "SELECT pro_name FROM products WHERE pro_id='$pro_id'";
						$resultProd = mysqli_query($con, $queryProd);
						$prodRow = mysqli_fetch_array($resultProd);
						
						$insufficientProducts[] = $prodRow['pro_name'] . " (Need: $ordered_qty, Available: $available_qty)";
					}
				} else {
					// No stock record for this product
					$canConfirm = false;
					error_log("NO STOCK RECORD for product $pro_id");
					
					// Get product name for error message
					$queryProd = "SELECT pro_name FROM products WHERE pro_id='$pro_id'";
					$resultProd = mysqli_query($con, $queryProd);
					$prodRow = mysqli_fetch_array($resultProd);
					
					$insufficientProducts[] = $prodRow['pro_name'] . " (Need: $ordered_qty, Available: 0)";
				}
			}
			
			error_log("Can confirm: " . ($canConfirm ? "YES" : "NO"));
			error_log("Insufficient products count: " . count($insufficientProducts));
			error_log("Insufficient products: " . print_r($insufficientProducts, true));
			
			if(!$canConfirm) {
				error_log("BLOCKING ORDER CONFIRMATION - INSUFFICIENT STOCK");
				$errorMsg = "Not enough quantity to confirm this order:\\n\\n";
				$errorMsg .= implode("\\n", $insufficientProducts);
				?>
				<script>
					alert("<?php echo $errorMsg; ?>");
					window.location.href='view_orders.php';
				</script>
				<?php
				exit();
			}
			else {
				// Update stock quantities in manufacturer_stock table
				$allUpdatesSuccessful = true;
				foreach($stockUpdates as $update) {
					$queryUpdateQuantity = "UPDATE manufacturer_stock 
											SET quantity='".$update['new_quantity']."' 
											WHERE manufacturer_id='$manufacturer_id' 
											AND product_id='".$update['pro_id']."'";
					if(!mysqli_query($con,$queryUpdateQuantity)) {
						$allUpdatesSuccessful = false;
						break;
					}
				}
				
				if($allUpdatesSuccessful) {
					// Confirm the order
					$queryConfirm = "UPDATE orders SET approved=1 WHERE order_id='$id'";
					if(mysqli_query($con,$queryConfirm)) {
						echo "<script> alert(\"Order has been confirmed successfully!\"); </script>";
						echo "<script> window.location.href='view_orders.php'; </script>";
					}
					else {
						echo "<script> alert(\"Error confirming order: " . mysqli_error($con) . "\"); </script>";
						echo "<script> window.location.href='view_orders.php'; </script>";
					}
				}
				else {
					echo "<script> alert(\"Error updating stock quantities\"); </script>";
					echo "<script> window.location.href='view_orders.php'; </script>";
				}
			}
		}
		else {
			header('Location:../index.php');
		}
	}
	else {
		header('Location:../index.php');
	}
?>