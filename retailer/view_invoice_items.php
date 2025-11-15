<?php
	require("../includes/config.php");
	session_start();
	if(isset($_SESSION['retailer_login'])) {
		if(isset($_GET['id'])){
			$invoice_id = $_GET['id'];
			$retailer_id = $_SESSION['retailer_id'];
			
			// Get invoice items from order_items table through the invoice's order_id (only for this retailer)
			$queryInvoiceItems = "SELECT oi.*, p.pro_name, p.pro_price, oi.quantity as quantity 
								FROM invoice i
								JOIN orders o ON i.order_id = o.order_id
								JOIN order_items oi ON i.order_id = oi.order_id
								JOIN products p ON oi.pro_id = p.pro_id 
								WHERE i.invoice_id='$invoice_id' AND o.retailer_id='$retailer_id'";
			$resultInvoiceItems = mysqli_query($con,$queryInvoiceItems);
			
			// Get invoice details with retailer and manufacturer info (only for this retailer)
			$querySelectInvoice = "SELECT i.*, o.order_id, o.date as order_date, r.retailer_name, r.retailer_phone, r.retailer_address, a.area_name, m.man_name, m.man_phone, m.man_address
									FROM invoice i 
									JOIN orders o ON i.order_id = o.order_id
									JOIN retailer r ON o.retailer_id = r.retailer_id 
									JOIN area a ON r.area_id = a.area_id
									JOIN manufacturer m ON i.manufacturer_id = m.man_id
									WHERE i.invoice_id='$invoice_id' AND o.retailer_id='$retailer_id'";
			$resultSelectInvoice = mysqli_query($con,$querySelectInvoice);
			$rowSelectInvoice = mysqli_fetch_array($resultSelectInvoice);
			
			// If no results, redirect (unauthorized access)
			if(!$rowSelectInvoice) {
				header('Location: view_my_invoices.php');
				exit;
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
	<title> View Invoice Details </title>
	<link rel="stylesheet" href="../includes/main_style.css" >
	<style>
		.invoice-header {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			color: white;
			padding: 25px;
			border-radius: 10px;
			margin-bottom: 25px;
			text-align: center;
			box-shadow: 0 4px 15px rgba(0,0,0,0.1);
		}
		
		.invoice-details {
			background: #f8f9fa;
			padding: 20px;
			border-radius: 8px;
			margin-bottom: 25px;
			border-left: 4px solid #28a745;
		}
		
		.invoice-details table {
			width: 100%;
			border-collapse: collapse;
		}
		
		.invoice-details td {
			padding: 8px 12px;
			border: none;
		}
		
		.invoice-details td:first-child {
			font-weight: bold;
			color: #495057;
			width: 200px;
		}
		
		.product-table {
			width: 100%;
			border-collapse: collapse;
			margin: 20px 0;
			background: white;
			border-radius: 8px;
			overflow: hidden;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
		}
		
		.product-table th {
			background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
			color: white;
			padding: 15px;
			text-align: left;
			font-weight: bold;
		}
		
		.product-table td {
			padding: 12px 15px;
			border-bottom: 1px solid #dee2e6;
		}
		
		.product-table tr:nth-child(even) {
			background-color: #f8f9fa;
		}
		
		.product-table tr:hover {
			background-color: #e3f2fd;
		}
		
		.total-row {
			background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
			color: white;
			font-weight: bold;
		}
		
		.total-row td {
			border: none;
		}
		
		.print-button {
			background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
			color: white;
			border: none;
			padding: 12px 24px;
			border-radius: 25px;
			cursor: pointer;
			font-size: 16px;
			font-weight: bold;
			margin-top: 20px;
			transition: all 0.3s ease;
			box-shadow: 0 4px 15px rgba(0,0,0,0.2);
		}
		
		.print-button:hover {
			transform: translateY(-2px);
			box-shadow: 0 6px 20px rgba(0,0,0,0.3);
		}
		
		.back-link {
			display: inline-block;
			margin-bottom: 20px;
			color: #007bff;
			text-decoration: none;
			font-weight: bold;
			padding: 8px 15px;
			border-radius: 5px;
			background: #e3f2fd;
			transition: all 0.3s ease;
		}
		
		.back-link:hover {
			background: #2196f3;
			color: white;
			text-decoration: none;
		}
		
		.comments-section {
			margin-top: 25px;
			padding: 15px;
			background: #fff3cd;
			border: 1px solid #ffeaa7;
			border-radius: 5px;
		}
		
		@media print {
			.print-button, .back-link { display: none; }
		}
	</style>
	<script type="text/javascript">     
        function PrintDiv() {
			document.getElementById("signature").style.display = "block";
			document.getElementById("footer").style.display = "block";
			var divToPrint = document.getElementById('divToPrint');
			var popupWin = window.open('', '_blank', '');
			popupWin.document.open();
			popupWin.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + '</html>');
			document.getElementById("signature").style.display = "none";
			document.getElementById("footer").style.display = "none";
			popupWin.document.close();
		}
     </script>
</head>
<body>
	<?php
		include("../includes/header.inc.php");
		include("../includes/nav_retailer.inc.php");
		include("../includes/aside_retailer.inc.php");
	?>
	<section>
		<a href="view_my_invoices.php" class="back-link">← Back to My Invoices</a>
		
		<div id="divToPrint" style="clear:both;" >
			<div class="invoice-header">
				<h1 style="margin: 0; font-size: 2.2em;">🧾 Sales Invoice</h1>
				<p style="margin: 5px 0 0 0; opacity: 0.9;">MSN Food Supply Chain Management System</p>
			</div>
			
			<div class="invoice-details">
				<table>
					<tr>
						<td><strong>📋 Invoice No:</strong></td>
						<td><?php echo $rowSelectInvoice['invoice_id']; ?></td>
						<td><strong>📦 Order No:</strong></td>
						<td><?php echo $rowSelectInvoice['order_id']; ?></td>
					</tr>
					<tr>
						<td><strong>🏪 Retailer:</strong></td>
						<td><?php echo $rowSelectInvoice['retailer_name']; ?> (<?php echo $rowSelectInvoice['area_name']; ?>)</td>
						<td><strong>🏭 Manufacturer:</strong></td>
						<td><?php echo $rowSelectInvoice['man_name']; ?></td>
					</tr>
					<tr>
						<td><strong>📅 Invoice Date:</strong></td>
						<td><?php echo date("d M Y", strtotime($rowSelectInvoice['invoice_date'])); ?></td>
						<td><strong>📋 Order Date:</strong></td>
						<td><?php echo date("d M Y", strtotime($rowSelectInvoice['order_date'])); ?></td>
					</tr>
				</table>
			</div>
		<table class="product-table">
			<thead>
				<tr>
					<th>Sr. No.</th>
					<th>Product Name</th>
					<th>Unit Price (৳)</th>
					<th>Quantity</th>
					<th>Total Amount (৳)</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$i = 1; 
				$grandTotal = 0;
				while($rowInvoiceItems = mysqli_fetch_array($resultInvoiceItems)) { 
					$itemTotal = $rowInvoiceItems['quantity'] * $rowInvoiceItems['pro_price'];
					$grandTotal += $itemTotal;
				?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo $rowInvoiceItems['pro_name']; ?></td>
					<td>৳<?php echo number_format($rowInvoiceItems['pro_price'], 2); ?></td>
					<td><?php echo $rowInvoiceItems['quantity']; ?></td>
					<td>৳<?php echo number_format($itemTotal, 2); ?></td>
				</tr>
				<?php $i++; } ?>
				<tr class="total-row">
					<td colspan="4" style="text-align:right; font-size: 18px;">
						<strong>💰 Grand Total:</strong>
					</td>
					<td style="font-size: 18px;">
						<strong>৳<?php echo number_format($rowSelectInvoice['total_amount'], 2); ?></strong>
					</td>
				</tr>
			</tbody>
		</table>
		<?php if(!empty($rowSelectInvoice['comments'])): ?>
		<div class="comments-section">
			<strong>📝 Comments:</strong><br/>
			<?php echo nl2br(htmlspecialchars($rowSelectInvoice['comments'])); ?>
		</div>
		<?php endif; ?>
		
		<div style="margin-top: 50px;">
			<p id="signature" style="float:right;display:none; font-weight: bold; border-top: 2px solid #000; padding-top: 10px; margin-top: 50px;">(Authorized Signatory)</p>
			<p id="footer" style="clear:both;display:none;padding: 20px 0;text-align:center; font-weight: bold; color: #28a745;">Thank you for your Business! 🙏</p>
		</div>
		</div>
		
		<input type="button" value="🖨️ Print Invoice" class="print-button" onclick="PrintDiv();" />
	</section>
	<?php
		include("../includes/footer.inc.php");
	?>
</body>
</html>