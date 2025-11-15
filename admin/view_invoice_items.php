<?php
	require("../includes/config.php");
	session_start();
	
	if(isset($_SESSION['admin_login']) && $_SESSION['admin_login'] == true) {
		$invoice_id = $_GET['id'];
		$print_mode = isset($_GET['print']) && $_GET['print'] == 1;
		
		// Get invoice details - Admin can see all invoices
		$querySelectInvoice = "SELECT i.*, o.order_id, o.date as order_date, r.retailer_name, r.retailer_phone, 
							  r.retailer_address, a.area_name, m.man_name, m.man_phone, m.man_address
							  FROM invoice i 
							  JOIN orders o ON i.order_id = o.order_id
							  JOIN retailer r ON o.retailer_id = r.retailer_id 
							  JOIN area a ON r.area_id = a.area_id
							  JOIN manufacturer m ON i.manufacturer_id = m.man_id
							  WHERE i.invoice_id='$invoice_id'";
		$resultSelectInvoice = mysqli_query($con,$querySelectInvoice);
		
		if(!$resultSelectInvoice || mysqli_num_rows($resultSelectInvoice) == 0) {
			echo "<script>alert('Invoice not found'); window.location.href='view_invoice.php';</script>";
			exit;
		}
		
		$rowSelectInvoice = mysqli_fetch_array($resultSelectInvoice);
		
		// Get invoice items - all products in this invoice
	$querySelectInvoiceItems = "SELECT oi.*, p.pro_name, p.pro_desc, u.unit_name
								FROM order_items oi 
								JOIN products p ON oi.pro_id = p.pro_id
								LEFT JOIN units u ON p.unit_id = u.unit_id
								WHERE oi.order_id='".$rowSelectInvoice['order_id']."'";
	$resultSelectInvoiceItems = mysqli_query($con,$querySelectInvoiceItems);
	} else {
		header('Location:../index.php');
		exit;
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Invoice Details - Invoice #<?php echo $rowSelectInvoice['invoice_id']; ?> [ADMIN]</title>
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
		.invoice-header { background: linear-gradient(135deg, #6175d8ff 0%, #764ba2 100%); color: white; padding: 25px; border-radius: 8px; margin-bottom: 25px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
		.admin-badge { background: #dc3545; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; margin-left: 10px; }
		.company-info, .customer-info { display: inline-block; width: 45%; vertical-align: top; padding: 20px; }
		.customer-info { margin-left: 5%; }
		.invoice-details { margin: 25px 0; }
		.print-button { background: #28a745; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 10px 8px; display: inline-block; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: all 0.3s; }
		.print-button:hover { background: #218838; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }
		.back-button { background: #6c757d; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 10px 8px; display: inline-block; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: all 0.3s; }
		.back-button:hover { background: #545b62; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }
		.info-section { background: linear-gradient(145deg, #f8f9fa, #e9ecef); border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin: 15px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
		.product-table { width: 100%; border-collapse: collapse; margin: 25px 0; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; }
		.product-table th, .product-table td { border: 1px solid #dee2e6; padding: 12px; text-align: left; }
		.product-table th { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; font-weight: bold; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; }
		.product-table tr:nth-child(even) { background-color: #f8f9fa; }
		.product-table tr:hover { background-color: #e3f2fd; }
		.total-section { background: linear-gradient(135deg, #d4edda, #c3e6cb); border: 2px solid #28a745; border-radius: 8px; padding: 25px; margin: 25px 0; text-align: right; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
		.section-title { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; margin: 30px 0 20px 0; font-size: 18px; font-weight: bold; }
	</style>
	<?php endif; ?>
</head>

<body>
	<?php if(!$print_mode): ?>
	<?php include("../includes/header.inc.php"); ?>
	<?php include("../includes/nav_admin.inc.php"); ?>
	<?php include("../includes/aside_admin.inc.php"); ?>
	<?php endif; ?>
	
	<section class="<?php echo $print_mode ? '' : 'main-section'; ?>">
		<?php if(!$print_mode): ?>
		<div class="no-print" style="margin-bottom: 25px;">
			<a href="view_invoice.php" class="back-button">← Back to All Invoices</a>
			<a href="view_invoice_items.php?id=<?php echo $invoice_id; ?>&print=1" target="_blank" class="print-button">🖨️ Print Invoice</a>
		</div>
		<?php endif; ?>
		
		<div class="invoice-header">
			<h1 style="margin-bottom: 10px; font-size: 28px;">INVOICE DETAILS</h1>
			<p style="margin: 0; font-size: 16px; opacity: 0.9;">Invoice #<?php echo $rowSelectInvoice['invoice_id']; ?></p>
			<?php if(!$print_mode): ?>
			<span class="admin-badge">ADMIN VIEW</span>
			<?php endif; ?>
		</div>

		<div class="invoice-details clearfix">
			<div class="company-info info-section">
				<h3 style="color: #2c3e50; border-bottom: 3px solid #007bff; padding-bottom: 8px; margin-bottom: 15px; font-size: 16px;">📦 From (Manufacturer)</h3>
				<p style="margin: 8px 0;"><strong style="color: #495057;"><?php echo $rowSelectInvoice['man_name']; ?></strong></p>
				<p style="margin: 8px 0; color: #6c757d;"><?php echo $rowSelectInvoice['man_address']; ?></p>
				<p style="margin: 8px 0;"><strong>📞 Phone:</strong> <span style="color: #007bff;"><?php echo $rowSelectInvoice['man_phone']; ?></span></p>
			</div>
			
			<div class="customer-info info-section">
				<h3 style="color: #2c3e50; border-bottom: 3px solid #dc3545; padding-bottom: 8px; margin-bottom: 15px; font-size: 16px;">🏪 To (Retailer)</h3>
				<p style="margin: 8px 0;"><strong style="color: #495057;"><?php echo $rowSelectInvoice['retailer_name']; ?></strong></p>
				<p style="margin: 8px 0; color: #6c757d;"><?php echo $rowSelectInvoice['retailer_address']; ?></p>
				<p style="margin: 8px 0;"><strong>📍 Area:</strong> <span style="color: #dc3545;"><?php echo $rowSelectInvoice['area_name']; ?></span></p>
				<p style="margin: 8px 0;"><strong>📞 Phone:</strong> <span style="color: #dc3545;"><?php echo $rowSelectInvoice['retailer_phone']; ?></span></p>
			</div>
		</div>

		<div class="clearfix"></div>

		<div class="info-section">
			<table style="width: 100%; border: none;">
				<tr>
					<td style="width: 25%; border: none; padding: 8px;"><strong>📅 Invoice Date:</strong></td>
					<td style="width: 25%; border: none; padding: 8px; color: #007bff; font-weight: bold;"><?php echo date("d M Y", strtotime($rowSelectInvoice['invoice_date'])); ?></td>
					<td style="width: 25%; border: none; padding: 8px;"><strong>📦 Order Date:</strong></td>
					<td style="width: 25%; border: none; padding: 8px; color: #28a745; font-weight: bold;"><?php echo date("d M Y", strtotime($rowSelectInvoice['order_date'])); ?></td>
				</tr>
				<tr>
					<td style="border: none; padding: 8px;"><strong>🔢 Order ID:</strong></td>
					<td style="border: none; padding: 8px; color: #6f42c1; font-weight: bold;">#<?php echo $rowSelectInvoice['order_id']; ?></td>
					<td style="border: none; padding: 8px;"><strong>📊 Status:</strong></td>
					<td style="border: none; padding: 8px;"><span style="color: <?php echo ($rowSelectInvoice['status'] == 'Paid') ? '#28a745' : '#ffc107'; ?>; font-weight: bold; background: <?php echo ($rowSelectInvoice['status'] == 'Paid') ? '#d4edda' : '#fff3cd'; ?>; padding: 4px 12px; border-radius: 15px; border: 1px solid <?php echo ($rowSelectInvoice['status'] == 'Paid') ? '#28a745' : '#ffc107'; ?>;"><?php echo $rowSelectInvoice['status']; ?></span></td>
				</tr>
			</table>
		</div>

		<h3 class="section-title">🛍️ Product Details</h3>
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
					<td><strong style="color: #495057;"><?php echo $rowSelectInvoiceItems['pro_name']; ?></strong></td>
					<td style="color: #6c757d;"><?php echo $rowSelectInvoiceItems['pro_desc'] ? $rowSelectInvoiceItems['pro_desc'] : 'N/A'; ?></td>
					<td style="text-align: center;"><span style="background: #f3e5f5; color: #7b1fa2; padding: 3px 8px; border-radius: 12px; font-size: 11px;"><?php echo $rowSelectInvoiceItems['unit_name'] ? $rowSelectInvoiceItems['unit_name'] : 'N/A'; ?></span></td>
					<td style="text-align: center;"><strong style="color: #ff5722; font-size: 16px;"><?php echo $rowSelectInvoiceItems['quantity']; ?></strong></td>
					<td style="text-align: right; font-family: monospace; color: #2e7d32;">৳<?php echo number_format($rowSelectInvoiceItems['price'], 2); ?></td>
					<td style="text-align: right; font-weight: bold; color: #1565c0; font-family: monospace; font-size: 15px;">৳<?php echo number_format($total_price, 2); ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>

		<div class="total-section">
			<h2 style="color: #2c3e50; margin: 0; font-size: 24px;">💰 Grand Total: <span style="color: #28a745; font-size: 28px;">৳<?php echo number_format($grand_total, 2); ?></span></h2>
			<p style="margin: 10px 0; color: #6c757d; font-style: italic;">Amount in BDT (Bangladeshi Taka)</p>
			<?php if(!$print_mode): ?>
			<p style="margin: 5px 0; color: #495057; font-size: 12px;">👑 <strong>Admin Access:</strong> Full system view enabled</p>
			<?php endif; ?>
		</div>

		<?php if($print_mode): ?>
		<div style="text-align: center; margin-top: 60px; padding-top: 30px; border-top: 2px solid #ddd;">
			<p style="color: #6c757d; font-size: 14px; margin: 5px 0;"><strong>🏢 MSN Food Supply Chain Management System</strong></p>
			<p style="color: #6c757d; font-size: 12px; margin: 5px 0;">This is a computer-generated invoice. No signature required.</p>
			<p style="color: #6c757d; font-size: 12px; margin: 5px 0;">Generated on: <?php echo date('d M Y, h:i A'); ?> | System Admin Access</p>
		</div>
		<?php endif; ?>

	</section>

	<?php if(!$print_mode): ?>
	<?php include("../includes/footer.inc.php"); ?>
	<?php endif; ?>
</body>
</html>