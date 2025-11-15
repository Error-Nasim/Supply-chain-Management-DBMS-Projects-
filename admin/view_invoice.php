<?php
	error_reporting(0);
	require("../includes/config.php");
	include("../includes/validate_data.php");
	session_start();
		if(isset($_SESSION['admin_login'])) {
			$error = "";
			$success = "";
			
			// Handle status update
			if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['update_status'])) {
				$invoice_id = intval($_POST['invoice_id']);
				$new_status = mysqli_real_escape_string($con, $_POST['status']);
				
				$query_updateStatus = "UPDATE invoice SET status='$new_status' WHERE invoice_id='$invoice_id'";
				if(mysqli_query($con, $query_updateStatus)) {
					$success = "Invoice status updated to $new_status successfully!";
				} else {
					$error = "Failed to update invoice status: " . mysqli_error($con);
				}
				
				// Show all invoices after update
				$query_selectInvoice = "SELECT i.*, o.order_id, o.date as order_date, r.retailer_name, r.retailer_phone, a.area_name, m.man_name 
										FROM invoice i 
										JOIN orders o ON i.order_id = o.order_id
										JOIN retailer r ON o.retailer_id = r.retailer_id 
										JOIN area a ON r.area_id = a.area_id
									JOIN manufacturer m ON i.manufacturer_id = m.man_id
									ORDER BY CASE WHEN i.status = 'Cancelled' THEN 1 ELSE 0 END, i.invoice_id DESC";
			$result_selectInvoice = mysqli_query($con,$query_selectInvoice);
			}
			
			$querySelectRetailer = "SELECT r.*, a.area_name FROM retailer r JOIN area a ON r.area_id = a.area_id";
			$resultSelectRetailer = mysqli_query($con,$querySelectRetailer);
			if($_SERVER['REQUEST_METHOD'] == "POST" && !isset($_POST['update_status'])) {
				if(isset($_POST['cmbFilter'])) {
					if(!empty($_POST['txtInvoiceId'])) {
						$result = validate_number($_POST['txtInvoiceId']);
						if($result == 1) {
							$invoice_id = $_POST['txtInvoiceId'];
							$query_selectInvoice = "SELECT i.*, o.order_id, o.date as order_date, r.retailer_name, r.retailer_phone, a.area_name, m.man_name 
													FROM invoice i 
													JOIN orders o ON i.order_id = o.order_id
													JOIN retailer r ON o.retailer_id = r.retailer_id 
													JOIN area a ON r.area_id = a.area_id
													JOIN manufacturer m ON i.manufacturer_id = m.man_id
													WHERE i.invoice_id='$invoice_id'";
							$result_selectInvoice = mysqli_query($con,$query_selectInvoice);
							$row_selectInvoice = mysqli_fetch_array($result_selectInvoice);
							if(empty($row_selectInvoice)){
							   $error = "* No invoice was found with this ID";
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
							$query_selectInvoice = "SELECT i.*, o.order_id, o.date as order_date, r.retailer_name, r.retailer_phone, a.area_name, m.man_name 
													FROM invoice i 
													JOIN orders o ON i.order_id = o.order_id
													JOIN retailer r ON o.retailer_id = r.retailer_id 
													JOIN area a ON r.area_id = a.area_id
													JOIN manufacturer m ON i.manufacturer_id = m.man_id
													WHERE i.order_id='$order_id'";
							$result_selectInvoice = mysqli_query($con,$query_selectInvoice);
							$row_selectInvoice = mysqli_fetch_array($result_selectInvoice);
							if(empty($row_selectInvoice)){
							   $error = "* No invoice was found with this order ID";
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
						$query_selectInvoice = "SELECT i.*, o.order_id, o.date as order_date, r.retailer_name, r.retailer_phone, a.area_name, m.man_name 
												FROM invoice i 
												JOIN orders o ON i.order_id = o.order_id
												JOIN retailer r ON o.retailer_id = r.retailer_id 
												JOIN area a ON r.area_id = a.area_id
												JOIN manufacturer m ON i.manufacturer_id = m.man_id
												WHERE o.retailer_id='$retailer_id' ORDER BY CASE WHEN i.status = 'Cancelled' THEN 1 ELSE 0 END, i.invoice_id DESC";
						$result_selectInvoice = mysqli_query($con,$query_selectInvoice);
						$row_selectInvoice = mysqli_fetch_array($result_selectInvoice);
						if(empty($row_selectInvoice)){
						   $error = "* No invoice was found of the selected Retailer";
						}
						else {
							mysqli_data_seek($result_selectInvoice,0);
						}
					}
					else if(!empty($_POST['txtDate'])) {
						$date = $_POST['txtDate'];
						$query_selectInvoice = "SELECT i.*, o.order_id, o.date as order_date, r.retailer_name, r.retailer_phone, a.area_name, m.man_name 
												FROM invoice i 
												JOIN orders o ON i.order_id = o.order_id
												JOIN retailer r ON o.retailer_id = r.retailer_id 
												JOIN area a ON r.area_id = a.area_id
												JOIN manufacturer m ON i.manufacturer_id = m.man_id
												WHERE i.invoice_date='$date'";
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
				$query_selectInvoice = "SELECT i.*, o.order_id, o.date as order_date, r.retailer_name, r.retailer_phone, a.area_name, m.man_name 
										FROM invoice i 
										JOIN orders o ON i.order_id = o.order_id
										JOIN retailer r ON o.retailer_id = r.retailer_id 
										JOIN area a ON r.area_id = a.area_id
									JOIN manufacturer m ON i.manufacturer_id = m.man_id
									ORDER BY CASE WHEN i.status = 'Cancelled' THEN 1 ELSE 0 END, i.invoice_id DESC";
			$result_selectInvoice = mysqli_query($con,$query_selectInvoice);
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
		include("../includes/nav_admin.inc.php");
		include("../includes/aside_admin.inc.php");
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
			<th> Manufacturer </th>
			<th> Invoice Date </th>
			<th> Order ID </th>
			<th> Total Amount (৳) </th>
			<th> Status </th>
			<th> Details </th>
			</tr>
			<?php while($row_selectInvoice = mysqli_fetch_array($result_selectInvoice)) { ?>
			<tr>
				<td> <strong>#<?php echo $row_selectInvoice['invoice_id']; ?></strong> </td>
				<td> 
					<?php echo $row_selectInvoice['retailer_name']; ?>
					<br><small style="color: #6c757d;"><?php echo $row_selectInvoice['area_name']; ?></small>
				</td>
				<td> 
					<?php echo $row_selectInvoice['man_name']; ?>
				</td>
				<td> <?php echo date("d M Y",strtotime($row_selectInvoice['invoice_date'])); ?> </td>
				<td> <strong>#<?php echo $row_selectInvoice['order_id']; ?></strong> </td>
				<td> <strong style="color: #28a745;">৳<?php echo number_format($row_selectInvoice['total_amount'], 2); ?></strong> </td>
				<td> 
					<?php 
						$status = $row_selectInvoice['status'];
						if($status == 'Paid') {
							$color = '#28a745'; $bg = '#d4edda';
						} elseif($status == 'Completed') {
							$color = '#007bff'; $bg = '#d1ecf1';
						} else {
							$color = '#ffc107'; $bg = '#fff3cd';
						}
					?>
					<span style="color: <?php echo $color; ?>; font-weight: bold; background: <?php echo $bg; ?>; padding: 3px 8px; border-radius: 12px; font-size: 11px;">
						<?php echo $status; ?>
					</span>
				</td>
				<td> <a href="view_invoice_items.php?id=<?php echo $row_selectInvoice['invoice_id']; ?>" style="background: #007bff; color: white; padding: 5px 12px; text-decoration: none; border-radius: 4px; font-size: 12px;">📄 Details</a> </td>
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