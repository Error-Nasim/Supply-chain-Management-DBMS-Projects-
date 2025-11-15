<?php
	require("../includes/config.php");
	session_start();
	$currentDate = date('Y-m-d');
	if(isset($_SESSION['manufacturer_login'])) {
		if($_SESSION['manufacturer_login'] == true) {
			$order_id = $_GET['id'];
			$manufacturer_id = $_SESSION['manufacturer_id'];
			
			// Get manufacturer details for invoice header
			$querySelectManufacturer = "SELECT * FROM manufacturer WHERE man_id='$manufacturer_id'";
			$resultManufacturer = mysqli_query($con,$querySelectManufacturer);
			$rowManufacturer = mysqli_fetch_array($resultManufacturer);
			
		// Only get order items for this manufacturer's assigned order
		$query_selectOrderItems = "SELECT oi.*, p.pro_name, p.pro_price, p.pro_desc, oi.quantity AS q, o.date, r.retailer_name, r.retailer_phone, a.area_name
								  FROM order_items oi 
								  JOIN products p ON oi.pro_id = p.pro_id 
								  JOIN orders o ON oi.order_id = o.order_id
								  JOIN retailer r ON o.retailer_id = r.retailer_id
								  JOIN area a ON r.area_id = a.area_id
								  WHERE oi.order_id='$order_id' AND o.assigned_manufacturer_id='$manufacturer_id'";
		$result_selectOrderItems = mysqli_query($con,$query_selectOrderItems);			$query_selectOrder = "SELECT o.date, o.status, r.retailer_name, r.retailer_phone, a.area_name 
								 FROM orders o 
								 JOIN retailer r ON o.retailer_id = r.retailer_id
								 JOIN area a ON r.area_id = a.area_id
								 WHERE o.order_id='$order_id'";
			$result_selectOrder = mysqli_query($con,$query_selectOrder);
			$row_selectOrder = mysqli_fetch_array($result_selectOrder);
			$query_selectInvoiceId = "SELECT `AUTO_INCREMENT` FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='scms' AND TABLE_NAME='invoice'";
			$result_selectInvoiceId = mysqli_query($con,$query_selectInvoiceId);
			$row_selectInvoiceId = mysqli_fetch_array($result_selectInvoiceId);
		}
		else {
			header('Location:../index.php');
		}
	}
	else {
		header('Location:../index.php');
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title> View Orders </title>
	<link rel="stylesheet" href="../includes/main_style.css" >
</head>
<body>
	<?php
		include("../includes/header.inc.php");
		include("../includes/nav_manufacturer.inc.php");
		include("../includes/aside_manufacturer.inc.php");
	?>
	<section>
		<h1>Invoice Summary</h1>
		<table class="table_infoFormat">
		<tr>
			<td> Invoice No: </td>
			<td> <?php echo $row_selectInvoiceId['AUTO_INCREMENT']; ?> </td>
		</tr>
		<tr>
			<td> Invoice Date: </td>
			<td> <?php echo date('d-m-Y'); ?> </td>
		</tr>
		<tr>
			<td> Order No: </td>
			<td> <?php echo $order_id; ?> </td>
		</tr>
		<tr>
			<td> Order Date: </td>
			<td> <?php echo date("d-m-Y",strtotime($row_selectOrder['date'])); ?> </td>
		</tr>
		</table>
		<form action="insert_invoice.php" method="POST" class="form">
		<input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
		<table class="table_invoiceFormat">
			<tr>
				<th> Products </th>
				<th> Unit Price (৳) </th>
				<th> Quantity </th>
				<th> Amount </th>
			</tr>
			<?php $i=1; while($row_selectOrderItems = mysqli_fetch_array($result_selectOrderItems)) { ?>
			<tr>
				<td> <?php echo $row_selectOrderItems['pro_name']; ?> </td>
				<td> ৳<?php echo $row_selectOrderItems['pro_price']; ?> </td>
				<td> <?php echo $row_selectOrderItems['q']; ?> </td>
				<td> ৳<?php echo $row_selectOrderItems['q']*$row_selectOrderItems['pro_price']; ?> </td>
			</tr>
			<?php $i++; } ?>
			<tr style="height:40px;vertical-align:bottom;">
				<td colspan="3" style="text-align:right;"> Grand Total (৳): </td>
				<td>
				<?php
					// Calculate total from order items
					mysqli_data_seek($result_selectOrderItems,0);
					$total_amount = 0;
					while($row_calc = mysqli_fetch_array($result_selectOrderItems)) {
						$total_amount += ($row_calc['q'] * $row_calc['pro_price']);
					}
					echo "৳" . number_format($total_amount, 2);
				?>
				</td>
			</tr>
		</table>
			<br/>
			<div style="margin: 20px 0; padding: 15px; background-color: #f0f8ff; border: 1px solid #0066cc; border-radius: 5px;">
				<h3 style="margin: 0; color: #0066cc;">Invoice Details</h3>
				<p><strong>From:</strong> <?php echo $rowManufacturer['man_name']; ?></p>
				<p><strong>To:</strong> <?php echo $row_selectOrder['retailer_name']; ?></p>
				<p><strong>Phone:</strong> <?php echo $row_selectOrder['retailer_phone']; ?></p>
				<p><strong>Area:</strong> <?php echo $row_selectOrder['area_name']; ?></p>
			</div>
			<br/>
			<input type="submit" value="Generate Invoice" class="submit_button" />
			<span class="error_message">
			<?php
				if(isset($_SESSION['error'])) {
					echo $_SESSION['error'];
					unset($_SESSION['error']);
				}
			?>
			</span>
		</form>
	</section>
	<?php
		include("../includes/footer.inc.php");
	?>
</body>
</html>