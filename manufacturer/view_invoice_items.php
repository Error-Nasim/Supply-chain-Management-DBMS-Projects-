<?php
	require("../includes/config.php");
	session_start();
	
	if(isset($_SESSION['manufacturer_login']) && $_SESSION['manufacturer_login'] == true) {
		$manufacturer_id = $_SESSION['manufacturer_id'];
		$invoice_id = $_GET['id'];
		$print_mode = isset($_GET['print']) && $_GET['print'] == 1;
		
		// Get invoice details - only if it belongs to this manufacturer
		$querySelectInvoice = "SELECT i.*, o.order_id, o.date as order_date, r.retailer_name, r.retailer_phone, 
							  r.retailer_address, a.area_name, m.man_name, m.man_phone, m.man_address
							  FROM invoice i 
							  JOIN orders o ON i.order_id = o.order_id
							  JOIN retailer r ON o.retailer_id = r.retailer_id 
							  JOIN area a ON r.area_id = a.area_id
							  JOIN manufacturer m ON i.manufacturer_id = m.man_id
							  WHERE i.invoice_id='$invoice_id' AND i.manufacturer_id='$manufacturer_id'";
		$resultSelectInvoice = mysqli_query($con,$querySelectInvoice);
		
		if(!$resultSelectInvoice || mysqli_num_rows($resultSelectInvoice) == 0) {
			echo "<script>alert('Invoice not found or access denied'); window.location.href='view_invoice.php';</script>";
			exit;
		}
		
		$rowSelectInvoice = mysqli_fetch_array($resultSelectInvoice);
		
	// Get invoice items - all products for this manufacturer's assigned order
	$querySelectInvoiceItems = "SELECT oi.*, p.pro_name, p.pro_desc, u.unit_name, o.assigned_manufacturer_id
								FROM order_items oi 
								JOIN orders o ON oi.order_id = o.order_id
								JOIN products p ON oi.pro_id = p.pro_id
								LEFT JOIN units u ON p.unit_id = u.unit_id
								WHERE oi.order_id='".$rowSelectInvoice['order_id']."' 
								AND o.assigned_manufacturer_id='$manufacturer_id'";
	$resultSelectInvoiceItems = mysqli_query($con,$querySelectInvoiceItems);
	} else {
		header('Location:../index.php');
		exit;
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Invoice Details - Invoice #<?php echo $rowSelectInvoice['invoice_id']; ?></title>
	<link rel="stylesheet" href="../includes/main_style.css">
	<?php if($print_mode): ?>
	<style>
		@media print {
			.no-print { display: none !important; }
			body { margin: 0; font-family: Arial, sans-serif; font-size: 12px; }
			.print-header { text-align: center; margin-bottom: 20px; }
			.invoice-details { margin: 20px 0; }
			.company-info { float: left; width: 45%; }
			.customer-info { float: right; width: 45%; }
			table { width: 100%; border-collapse: collapse; margin: 20px 0; }
			th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 10px; }
			th { background-color: #f2f2f2; }
			.total-section { text-align: right; margin: 20px 0; font-weight: bold; }
		}
	</style>
	<script>
		window.onload = function() { window.print(); }
	</script>
	<?php else: ?>
	<style>
		.invoice-header { background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px; text-align: center; }
		.company-info, .customer-info { display: inline-block; width: 45%; vertical-align: top; padding: 15px; }
		.customer-info { margin-left: 5%; }
		.invoice-details { margin: 20px 0; }
		.print-button { background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 10px 5px; display: inline-block; }
		.print-button:hover { background: #218838; }
		.back-button { background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 10px 5px; display: inline-block; }
		.back-button:hover { background: #545b62; }
		.info-section { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 15px; margin: 10px 0; }
		.product-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
		.product-table th, .product-table td { border: 1px solid #dee2e6; padding: 10px; text-align: left; }
		.product-table th { background-color: #e9ecef; font-weight: bold; }
		.total-section { background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; padding: 20px; margin: 20px 0; text-align: right; }
	</style>
	<?php endif; ?>
</head>

<body>
	<?php if(!$print_mode): ?>
	<?php include("../includes/header.inc.php"); ?>
	<?php include("../includes/nav_manufacturer.inc.php"); ?>
	<?php include("../includes/aside_manufacturer.inc.php"); ?>
	<?php endif; ?>
	
	<section class="<?php echo $print_mode ? '' : 'main-section'; ?>">
		<?php if(!$print_mode): ?>
		<div class="no-print" style="margin-bottom: 20px;">
			<a href="view_invoice.php" class="back-button">← Back to Invoices</a>
			<a href="view_invoice_items.php?id=<?php echo $invoice_id; ?>&print=1" target="_blank" class="print-button">🖨️ Print Invoice</a>
		</div>
		<?php endif; ?>
		
		<div class="invoice-header">
			<h1 style="color: #2c3e50; margin-bottom: 10px;">INVOICE</h1>
			<p style="color: #6c757d; margin: 0;">Invoice #<?php echo $rowSelectInvoice['invoice_id']; ?></p>
		</div>

		<div class="invoice-details clearfix">
			<div class="company-info info-section">
				<h3 style="color: #2c3e50; border-bottom: 2px solid #007bff; padding-bottom: 5px; margin-bottom: 15px;">From (Manufacturer)</h3>
				<p><strong><?php echo $rowSelectInvoice['man_name']; ?></strong></p>
				<p><?php echo $rowSelectInvoice['man_address']; ?></p>
				<p><strong>Phone:</strong> <?php echo $rowSelectInvoice['man_phone']; ?></p>
			</div>
			
			<div class="customer-info info-section">
				<h3 style="color: #2c3e50; border-bottom: 2px solid #dc3545; padding-bottom: 5px; margin-bottom: 15px;">To (Retailer)</h3>
				<p><strong><?php echo $rowSelectInvoice['retailer_name']; ?></strong></p>
				<p><?php echo $rowSelectInvoice['retailer_address']; ?></p>
				<p><strong>Area:</strong> <?php echo $rowSelectInvoice['area_name']; ?></p>
				<p><strong>Phone:</strong> <?php echo $rowSelectInvoice['retailer_phone']; ?></p>
			</div>
		</div>

		<div class="clearfix"></div>

		<div class="info-section">
			<table style="width: 100%;">
				<tr>
					<td style="width: 25%;"><strong>Invoice Date:</strong></td>
					<td style="width: 25%;"><?php echo date("d M Y", strtotime($rowSelectInvoice['invoice_date'])); ?></td>
					<td style="width: 25%;"><strong>Order Date:</strong></td>
					<td style="width: 25%;"><?php echo date("d M Y", strtotime($rowSelectInvoice['order_date'])); ?></td>
				</tr>
				<tr>
					<td><strong>Order ID:</strong></td>
					<td><?php echo $rowSelectInvoice['order_id']; ?></td>
					<td><strong>Status:</strong></td>
					<td><span style="color: <?php echo ($rowSelectInvoice['status'] == 'Paid') ? '#28a745' : '#ffc107'; ?>; font-weight: bold;"><?php echo $rowSelectInvoice['status']; ?></span></td>
				</tr>
			</table>
		</div>

		<h3 style="color: #2c3e50; border-bottom: 2px solid #28a745; padding-bottom: 10px; margin: 30px 0 15px 0;">Product Details</h3>
		<table class="product-table">
			<thead>
				<tr>
					<th>Product Name</th>
					<th>Description</th>
					<th>Unit</th>
					<th>Quantity</th>
					<th>Unit Price (৳)</th>
					<th>Total Price (৳)</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$grand_total = 0;
				while($rowSelectInvoiceItems = mysqli_fetch_array($resultSelectInvoiceItems)) { 
					$total_price = $rowSelectInvoiceItems['quantity'] * $rowSelectInvoiceItems['price'];
					$grand_total += $total_price;
				?>
				<tr>
					<td><strong><?php echo $rowSelectInvoiceItems['pro_name']; ?></strong></td>
					<td><?php echo $rowSelectInvoiceItems['pro_desc'] ? $rowSelectInvoiceItems['pro_desc'] : 'N/A'; ?></td>
					<td><?php echo $rowSelectInvoiceItems['unit_name'] ? $rowSelectInvoiceItems['unit_name'] : 'N/A'; ?></td>
					<td style="text-align: center;"><strong><?php echo $rowSelectInvoiceItems['quantity']; ?></strong></td>
					<td style="text-align: right;">৳<?php echo number_format($rowSelectInvoiceItems['price'], 2); ?></td>
					<td style="text-align: right; font-weight: bold; color: #28a745;">৳<?php echo number_format($total_price, 2); ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>

		<div class="total-section">
			<h2 style="color: #2c3e50; margin: 0;">Grand Total: <span style="color: #28a745;">৳<?php echo number_format($grand_total, 2); ?></span></h2>
			<p style="margin: 5px 0; color: #6c757d;">Amount in BDT (Bangladeshi Taka)</p>
		</div>

		<?php if($print_mode): ?>
		<div style="text-align: center; margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd;">
			<p style="color: #6c757d; font-size: 12px;">This is a computer-generated invoice. No signature required.</p>
			<p style="color: #6c757d; font-size: 12px;">Generated on: <?php echo date('d M Y, h:i A'); ?></p>
		</div>
		<?php endif; ?>

	</section>

	<?php if(!$print_mode): ?>
	<?php include("../includes/footer.inc.php"); ?>
	<?php endif; ?>
</body>
</html>