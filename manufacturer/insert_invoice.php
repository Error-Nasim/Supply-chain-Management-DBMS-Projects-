<?php
	require("../includes/config.php");
	session_start();
	$currentDate = date('Y-m-d');
	
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		if(isset($_SESSION['manufacturer_login']) && $_SESSION['manufacturer_login'] == true) {
		$manufacturer_id = $_SESSION['manufacturer_id'];
		$order_id = $_POST['order_id'];
		
		// Validate that the order is assigned to this manufacturer
		$checkOrder = "SELECT COUNT(*) as count FROM orders o 
					   WHERE o.order_id='$order_id' AND o.assigned_manufacturer_id='$manufacturer_id'";
		$checkResult = mysqli_query($con, $checkOrder);
		$checkRow = mysqli_fetch_array($checkResult);
		
		if($checkRow['count'] == 0) {
			echo "<script> alert(\"This order is not assigned to you.\"); </script>";
			header("Refresh:2;url=view_orders.php");
			exit;
		}
		
		// Calculate total amount for all items in the order
		$query_calcTotal = "SELECT SUM(oi.quantity * oi.price) as total_amount 
						   FROM order_items oi 
						   WHERE oi.order_id='$order_id'";
		$result_calcTotal = mysqli_query($con,$query_calcTotal);
		$row_calcTotal = mysqli_fetch_array($result_calcTotal);
		$total_amount = $row_calcTotal['total_amount'];			$query_selectInvoiceId = "SELECT `AUTO_INCREMENT` FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='scms' AND TABLE_NAME='invoice'";
			$result_selectInvoiceId = mysqli_query($con,$query_selectInvoiceId);
			$row_selectInvoiceId = mysqli_fetch_array($result_selectInvoiceId);
			$invoice_id = $row_selectInvoiceId['AUTO_INCREMENT'];
			
			$queryInsertInvoice = "INSERT INTO invoice(order_id,manufacturer_id,invoice_date,total_amount,status) VALUES('$order_id','$manufacturer_id','$currentDate','$total_amount','Pending')";
			if(mysqli_query($con,$queryInsertInvoice)) {
				// Update order status to completed for this manufacturer's portion
				$queryUpdateStatus = "UPDATE orders SET status=1 WHERE order_id='$order_id'";
				if(mysqli_query($con,$queryUpdateStatus)) {
					echo "<script> alert(\"Invoice Generated Successfully for ৳$total_amount\"); </script>";
					header("Refresh:2;url=view_invoice.php");
				} else {
					echo "<script> alert(\"Invoice created but could not update order status\"); </script>";
					header("Refresh:2;url=view_invoice.php");
				}
			}
			else {
				echo "There was some issue";
			}
		}
	}
?>