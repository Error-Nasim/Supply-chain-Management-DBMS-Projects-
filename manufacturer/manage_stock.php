<?php
	include("../includes/config.php");
	session_start();
	if(isset($_SESSION['manufacturer_login'])) {
		if($_SESSION['manufacturer_login'] == true) {
			$manufacturer_id = $_SESSION['manufacturer_id'];
			
			// Handle stock updates
			if($_SERVER['REQUEST_METHOD'] == "POST") {
				if(isset($_POST['txtQuantity'])){
					$arrayQuantity = $_POST['txtQuantity'];
					$updated_count = 0;
					$failed_count = 0;
					
					foreach($arrayQuantity as $product_id => $value) {
						// Validate quantity is numeric and non-negative
						if(is_numeric($value) && $value >= 0) {
							// Insert or update stock for this manufacturer-product combination
							$queryUpdateStock = "INSERT INTO manufacturer_stock (manufacturer_id, product_id, quantity) 
												VALUES ('$manufacturer_id', '$product_id', '$value')
												ON DUPLICATE KEY UPDATE quantity = '$value'";
							if(mysqli_query($con, $queryUpdateStock)) {
								$updated_count++;
							} else {
								$failed_count++;
							}
						} else {
							$failed_count++;
						}
					}
					
					if($updated_count > 0 && $failed_count == 0) {
						$success = "Stock updated successfully for $updated_count product(s)";
					} else if($updated_count > 0 && $failed_count > 0) {
						$warning = "Stock updated for $updated_count product(s), but $failed_count failed";
					} else {
						$error = "No stock was updated. Please check quantities are valid numbers.";
					}
				}
			}
			
			// Get ALL products with manufacturer's stock quantity
			$querySelectProduct = "SELECT p.*, u.unit_name, 
								   COALESCE(ms.quantity, 0) as my_quantity,
								   ms.last_updated
								   FROM products p 
								   JOIN units u ON p.unit_id = u.unit_id 
								   LEFT JOIN manufacturer_stock ms ON p.pro_id = ms.product_id AND ms.manufacturer_id = '$manufacturer_id'
								   ORDER BY p.pro_name";
			$resultSelectProduct = mysqli_query($con,$querySelectProduct);
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
	<title>Manage Stock - Manufacturer</title>
	<link rel="stylesheet" href="../includes/main_style.css" >
	<style>
		.stock-input {
			width: 100px;
			text-align: center;
			padding: 8px;
			border: 2px solid #ddd;
			border-radius: 4px;
			font-size: 14px;
		}
		.stock-input:focus {
			border-color: #007bff;
			outline: none;
			box-shadow: 0 0 5px rgba(0,123,255,0.3);
		}
		.stock-row:hover {
			background-color: #f8f9fa;
		}
		.stock-table {
			margin-top: 20px;
		}
		.stock-info {
			background: #e3f2fd;
			padding: 15px;
			border-radius: 8px;
			margin-bottom: 20px;
		}
		.low-stock {
			background-color: #fff3cd;
		}
		.out-of-stock {
			background-color: #f8d7da;
		}
		.btn-update {
			background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
			color: white;
			border: none;
			padding: 12px 30px;
			border-radius: 6px;
			cursor: pointer;
			font-size: 16px;
			font-weight: bold;
			margin-top: 20px;
		}
		.btn-update:hover {
			transform: translateY(-2px);
			box-shadow: 0 4px 8px rgba(0,0,0,0.2);
		}
		.success-msg {
			background: #d4edda;
			color: #155724;
			padding: 12px;
			border-radius: 6px;
			margin-bottom: 20px;
		}
		.error-msg {
			background: #f8d7da;
			color: #721c24;
			padding: 12px;
			border-radius: 6px;
			margin-bottom: 20px;
		}
	</style>
</head>
<body>
	<?php include("../includes/header.inc.php"); ?>
	<?php include("../includes/nav_manufacturer.inc.php"); ?>
	<?php include("../includes/aside_manufacturer.inc.php"); ?>
	<section>
		<h1>📦 Manage My Stock</h1>
		
		<?php if(isset($success)) { ?>
			<div class="success-msg">✓ <?php echo $success; ?></div>
		<?php } ?>
		
		<?php if(isset($error)) { ?>
			<div class="error-msg">✗ <?php echo $error; ?></div>
		<?php } ?>
		
		<div class="stock-info">
			<h3>ℹ️ Stock Management Information</h3>
			<p>• <strong>All Products Shown:</strong> You can manage stock for ANY product in the system</p>
			<p>• <strong>Your Quantities Only:</strong> Quantities shown and edited are YOUR stock levels only</p>
			<p>• <strong>Other Manufacturers:</strong> Other manufacturers have their own separate stock for the same products</p>
			<p>• Enter quantity "0" for products you don't want to sell</p>
		</div>
		
		<form method="POST" action="">
			<table class="stock-table">
				<tr>
					<th>Product ID</th>
					<th>Product Name</th>
					<th>Description</th>
					<th>Price (৳)</th>
					<th>Unit</th>
					<th>My Current Stock</th>
					<th>Last Updated</th>
				</tr>
				<?php 
				$count = 0;
				while($row = mysqli_fetch_array($resultSelectProduct)) { 
					$stockClass = '';
					if($row['my_quantity'] == 0) {
						$stockClass = 'out-of-stock';
					} else if($row['my_quantity'] < 10) {
						$stockClass = 'low-stock';
					}
				?>
				<tr class="stock-row <?php echo $stockClass; ?>">
					<td><?php echo $row['pro_id']; ?></td>
					<td><strong><?php echo $row['pro_name']; ?></strong></td>
					<td><?php echo $row['pro_desc']; ?></td>
					<td>৳<?php echo number_format($row['pro_price'], 2); ?></td>
					<td><?php echo $row['unit_name']; ?></td>
					<td>
						<input type="number" name="txtQuantity[<?php echo $row['pro_id']; ?>]" 
							   value="<?php echo $row['my_quantity']; ?>" 
							   min="0" 
							   class="stock-input" 
							   required>
					</td>
					<td><?php echo $row['last_updated'] ? date('d-m-Y H:i', strtotime($row['last_updated'])) : 'Never'; ?></td>
				</tr>
				<?php 
					$count++;
				} 
				?>
			</table>
			
			<?php if($count > 0) { ?>
				<button type="submit" class="btn-update">💾 Update All Stock Quantities</button>
			<?php } else { ?>
				<p>No products found in the system.</p>
			<?php } ?>
		</form>
	</section>
	<?php include("../includes/footer.inc.php"); ?>
</body>
</html>
