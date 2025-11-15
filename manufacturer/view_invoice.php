<?php
	error_reporting(0);
	require("../includes/config.php");
	include("../includes/validate_data.php");
	session_start();
		if(isset($_SESSION['manufacturer_login'])) {
			if($_SESSION['manufacturer_login'] == true) {
				$error = "";
				$success = "";
				$manufacturer_id = $_SESSION['manufacturer_id'];
				
				// Handle status update
			if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['update_status'])) {
				$invoice_id = intval($_POST['invoice_id']);
				$new_status = mysqli_real_escape_string($con, $_POST['status']);
				
				// Verify invoice belongs to this manufacturer
				$query_checkInvoice = "SELECT invoice_id, status FROM invoice WHERE invoice_id='$invoice_id' AND manufacturer_id='$manufacturer_id'";
				$result_checkInvoice = mysqli_query($con, $query_checkInvoice);
				
				if(mysqli_num_rows($result_checkInvoice) > 0) {
					$invoice_data = mysqli_fetch_array($result_checkInvoice);
					
					// Check if invoice is already completed - cannot be changed
					if($invoice_data['status'] == 'Completed') {
						$error = "Cannot change status of completed invoices. Completed status is permanent.";
					} else {
						$query_updateStatus = "UPDATE invoice SET status='$new_status' WHERE invoice_id='$invoice_id'";
						if(mysqli_query($con, $query_updateStatus)) {
							$success = "Invoice status updated to $new_status successfully!";
						} else {
							$error = "Failed to update invoice status: " . mysqli_error($con);
						}
					}
				} else {
					$error = "Unauthorized access to invoice";
				}					// Show all invoices after update
					$query_selectInvoice = "SELECT i.*, o.order_id, r.retailer_name, r.retailer_phone, a.area_name, o.date as order_date
											FROM invoice i 
											JOIN orders o ON i.order_id = o.order_id
											JOIN retailer r ON o.retailer_id = r.retailer_id 
											JOIN area a ON r.area_id = a.area_id
										WHERE i.manufacturer_id='$manufacturer_id'
										ORDER BY CASE WHEN i.status = 'Cancelled' THEN 1 ELSE 0 END, i.invoice_date DESC";
					$result_selectInvoice = mysqli_query($con,$query_selectInvoice);
				}
				
			// Get retailers for dropdown (only those who have orders assigned to this manufacturer)
			$querySelectRetailer = "SELECT DISTINCT r.*, a.area_name 
									FROM retailer r 
									JOIN area a ON r.area_id = a.area_id
									JOIN orders o ON r.retailer_id = o.retailer_id
									WHERE o.assigned_manufacturer_id = '$manufacturer_id'";
			$resultSelectRetailer = mysqli_query($con,$querySelectRetailer);			if($_SERVER['REQUEST_METHOD'] == "POST" && !isset($_POST['update_status'])) {
				if(isset($_POST['cmbFilter'])) {
					if(!empty($_POST['txtInvoiceId'])) {
						$result = validate_number($_POST['txtInvoiceId']);
						if($result == 1) {
							$invoice_id = $_POST['txtInvoiceId'];
							$query_selectInvoice = "SELECT i.*, o.order_id, r.retailer_name, r.retailer_phone, a.area_name, o.date as order_date
													FROM invoice i 
													JOIN orders o ON i.order_id = o.order_id
													JOIN retailer r ON o.retailer_id = r.retailer_id 
													JOIN area a ON r.area_id = a.area_id
													WHERE i.invoice_id='$invoice_id' AND i.manufacturer_id='$manufacturer_id'";
							$result_selectInvoice = mysqli_query($con,$query_selectInvoice);
							$row_selectInvoice = mysqli_fetch_array($result_selectInvoice);
							if(empty($row_selectInvoice)){
							   $error = "* No invoice was found with this ID for your manufacturer";
							}
							else {
								mysqli_data_seek($result_selectInvoice,0);
							}
						}
						else {
							$error = "* Invalid ID";
						}
					}
					else if(!empty($_POST['txtOrderId'])) {
						$result = validate_number($_POST['txtOrderId']);
						if($result == 1) {
							$order_id = $_POST['txtOrderId'];
							$query_selectInvoice = "SELECT i.*, o.order_id, r.retailer_name, r.retailer_phone, a.area_name, o.date as order_date
													FROM invoice i 
													JOIN orders o ON i.order_id = o.order_id
													JOIN retailer r ON o.retailer_id = r.retailer_id 
													JOIN area a ON r.area_id = a.area_id
													WHERE i.order_id='$order_id' AND i.manufacturer_id='$manufacturer_id'";
							$result_selectInvoice = mysqli_query($con,$query_selectInvoice);
							$row_selectInvoice = mysqli_fetch_array($result_selectInvoice);
							if(empty($row_selectInvoice)){
							   $error = "* No invoice was found for this order ID for your manufacturer";
							}
							else {
								mysqli_data_seek($result_selectInvoice,0);
							}
						}
						else {
							$error = "* Invalid ID";
						}
					}
					else if(!empty($_POST['cmbRetailer'])) {
						$retailer_id = $_POST['cmbRetailer'];
						$query_selectInvoice = "SELECT i.*, o.order_id, r.retailer_name, r.retailer_phone, a.area_name, o.date as order_date
												FROM invoice i 
												JOIN orders o ON i.order_id = o.order_id
												JOIN retailer r ON o.retailer_id = r.retailer_id 
												JOIN area a ON r.area_id = a.area_id
											WHERE r.retailer_id='$retailer_id' AND i.manufacturer_id='$manufacturer_id' 
											ORDER BY CASE WHEN i.status = 'Cancelled' THEN 1 ELSE 0 END, i.invoice_id DESC";
						$result_selectInvoice = mysqli_query($con,$query_selectInvoice);
						$row_selectInvoice = mysqli_fetch_array($result_selectInvoice);
						if(empty($row_selectInvoice)){
						   $error = "* No invoice was found for the selected Retailer";
						}
						else {
							mysqli_data_seek($result_selectInvoice,0);
						}
					}
					else if(!empty($_POST['txtDate'])) {
						$date = $_POST['txtDate'];
						$query_selectInvoice = "SELECT i.*, o.order_id, r.retailer_name, r.retailer_phone, a.area_name, o.date as order_date
												FROM invoice i 
												JOIN orders o ON i.order_id = o.order_id
												JOIN retailer r ON o.retailer_id = r.retailer_id 
												JOIN area a ON r.area_id = a.area_id
											WHERE i.invoice_date='$date' AND i.manufacturer_id='$manufacturer_id' 
											ORDER BY CASE WHEN i.status = 'Cancelled' THEN 1 ELSE 0 END, i.invoice_id DESC";
						$result_selectInvoice = mysqli_query($con,$query_selectInvoice);
						$row_selectInvoice = mysqli_fetch_array($result_selectInvoice);
						if(empty($row_selectInvoice)){
						   $error = "* No invoice was found with the selected Date";
						}
						else {
							mysqli_data_seek($result_selectInvoice,0);
						}
						
					}
					else {
						$error = "* Please enter the data to search for.";
					}
				}
				else {
					$error = "Please choose an option to search for.";
				}
			}
			else {
				// Show all invoices for this manufacturer
				$query_selectInvoice = "SELECT i.*, o.order_id, r.retailer_name, r.retailer_phone, a.area_name, o.date as order_date
										FROM invoice i 
										JOIN orders o ON i.order_id = o.order_id
										JOIN retailer r ON o.retailer_id = r.retailer_id 
										JOIN area a ON r.area_id = a.area_id
									WHERE i.manufacturer_id='$manufacturer_id' 
									ORDER BY CASE WHEN i.status = 'Cancelled' THEN 1 ELSE 0 END, i.invoice_id DESC";
				$result_selectInvoice = mysqli_query($con,$query_selectInvoice);
			}
		}
	}
	else {
		header('Location:../index.php');
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title> View Invoices </title>
	<link rel="stylesheet" href="../includes/main_style.css" >
	<link rel="stylesheet" href="css/smoothness/jquery-ui.css">
	<script type="text/javascript" src="../includes/jquery.js"> </script>
	<script src="js/jquery-ui.js"></script>
	<script>
  $(function() {
    $( "#datepicker" ).datepicker({
     changeMonth:true,
     changeYear:true,
     yearRange:"-100:+0",
     dateFormat:"yy-mm-dd"
  });
  });
  </script>
</head>
<body>
	<?php
		include("../includes/header.inc.php");
		include("../includes/nav_manufacturer.inc.php");
		include("../includes/aside_manufacturer.inc.php");
	?>
	<section>
		<h1>Invoices</h1>
		<form action="" method="POST" class="form">
		Search By: 
		<div class="input-box">
		<select name="cmbFilter" id="cmbFilter">
		<option value="" disabled selected>-- Search By --</option>
		<option value="invoiceId">  Invoice Id </option>
		<option value="orderId"> Order ID </option>
		<option value="retailer"> Retailer </option>
		<option value="date"> Date </option>
		</select>
		</div>
		
		<div class="input-box"> <input type="text" name="txtInvoiceId" id="txtInvoiceId" style="display:none;" /> </div>
		<div class="input-box"> <input type="text" name="txtOrderId" id="txtOrderId" style="display:none;" /> </div>
		<div class="input-box">
		<select name="cmbRetailer" id="cmbRetailer" style="display:none;">
			<option value="" disabled selected>-- Select Retailer --</option>
			<?php while($rowSelectRetailer = mysqli_fetch_array($resultSelectRetailer)) { ?>
			<option value="<?php echo $rowSelectRetailer['retailer_id']; ?>"><?php echo $rowSelectRetailer['retailer_name']." (".$rowSelectRetailer['area_name'].")"; ?></option>
			<?php } ?>
		</select>
		</div>
		<div class="input-box"> <input type="text" id="datepicker" name="txtDate" style="display:none;"/> </div>
		<input type="submit" class="submit_button" value="Search" />
		<?php if(!empty($error)): ?>
			<span class="error_message"><?php echo $error; ?></span>
		<?php endif; ?>
		</form>
		
		<?php if(!empty($success)) { ?>
			<div style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 12px; border-radius: 5px; margin: 15px 0;">
				<strong>✓ Success:</strong> <?php echo $success; ?>
			</div>
		<?php } ?>
	
		<form action="" method="POST" class="form">
		<table class="table_displayData" style="margin-top:20px;">
			<tr>
				<th> Invoice ID </th>
				<th> Retailer </th>
				<th> Invoice Date </th>
				<th> Order ID </th>
				<th> Total Amount (৳) </th>
				<th> Status </th>
				<th> Update Status </th>
				<th> Details </th>
			</tr>
			<?php while($row_selectInvoice = mysqli_fetch_array($result_selectInvoice)) { ?>
			<tr>
				<td> <?php echo $row_selectInvoice['invoice_id']; ?> </td>
				<td> <?php echo $row_selectInvoice['retailer_name']; ?><br><small><?php echo $row_selectInvoice['area_name']; ?></small> </td>
				<td> <?php echo date("d-m-Y",strtotime($row_selectInvoice['invoice_date'])); ?> </td>
				<td> <?php echo $row_selectInvoice['order_id']; ?> </td>
				<td> ৳<?php echo number_format($row_selectInvoice['total_amount'], 2); ?> </td>
				<td> 
					<?php 
						$status = $row_selectInvoice['status'];
						if($status == 'Paid') {
							$color = 'green';
						} elseif($status == 'Completed') {
							$color = 'blue';
						} else {
							$color = 'orange';
						}
					?>
					<span style="color: <?php echo $color; ?>;">
						<?php echo $status; ?>
					</span>
				</td>
			<td>
				<?php if($row_selectInvoice['status'] == 'Completed'): ?>
					<!-- Completed status is permanent - show locked status -->
					<span style="color: #0056b3; font-weight: bold; background: #d1ecf1; padding: 5px 10px; border-radius: 12px; font-size: 12px;">
						🔒 Completed
					</span>
				<?php else: ?>
					<!-- Allow status update for non-completed invoices -->
					<form method="POST" style="display: inline-block;">
						<input type="hidden" name="invoice_id" value="<?php echo $row_selectInvoice['invoice_id']; ?>">
						<select name="status" style="padding: 4px 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px;">
							<option value="Pending" <?php echo ($row_selectInvoice['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
							<option value="Paid" <?php echo ($row_selectInvoice['status'] == 'Paid') ? 'selected' : ''; ?>>Paid</option>
							<option value="Completed">Completed</option>
						</select>
						<button type="submit" name="update_status" style="background: #28a745; color: white; padding: 4px 10px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; margin-left: 5px;">✓ Update</button>
					</form>
				<?php endif; ?>
			</td>
				<td> 
					<a href="view_invoice_items.php?id=<?php echo $row_selectInvoice['invoice_id']; ?>">Details</a>
				</td>
			</tr>
			<?php } ?>
		</table>
		</form>
	</section>
	<?php
		include("../includes/footer.inc.php");
	?>
	<script type="text/javascript">
		$('#cmbFilter').change(function() {
			var selected = $(this).val();
			if(selected == "invoiceId"){
				$('#txtInvoiceId').show();
				$('#txtOrderId').hide();
				$('#datepicker').hide();
				$('#cmbRetailer').hide();
			}
			else if (selected == "orderId"){
				$('#txtInvoiceId').hide();
				$('#txtOrderId').show();
				$('#datepicker').hide();
				$('#cmbRetailer').hide();
			}
			else if (selected == "retailer"){
				$('#txtInvoiceId').hide();
				$('#txtOrderId').hide();
				$('#datepicker').hide();
				$('#cmbRetailer').show();
			}
			else if (selected == "date"){
				$('#txtInvoiceId').hide();
				$('#txtOrderId').hide();
				$('#datepicker').show();
				$('#cmbRetailer').hide();
			}
		});
	</script>
</body>
</html>