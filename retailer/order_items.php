<?php
	require("../includes/config.php");
	session_start();
	if(isset($_SESSION['retailer_login']) || isset($_SESSION['admin_login'])) {
			// Get all products with total available quantity from all manufacturers
			$query_selectProducts = "SELECT p.*, COALESCE(SUM(ms.quantity), 0) as available_quantity
									FROM products p 
									LEFT JOIN manufacturer_stock ms ON p.pro_id = ms.product_id
									GROUP BY p.pro_id
									ORDER BY p.pro_name";
			$result_selectProducts = mysqli_query($con,$query_selectProducts);
	}
	else {
		header('Location:../index.php');
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title> Order Items </title>
	<link rel="stylesheet" href="../includes/main_style.css" >
</head>
<body>
	<?php
		include("../includes/header.inc.php");
		include("../includes/nav_retailer.inc.php");
		include("../includes/aside_retailer.inc.php");
	?>
	<section>
		<h1>Order Items</h1>
		
		<?php
		// Display error messages
		if(isset($_GET['error'])) {
			$error_type = $_GET['error'];
			$error_message = "";
			switch($error_type) {
				case 'no_items':
					$error_message = "Please select at least one item with quantity to place an order.";
					break;
				case 'invalid_total':
					$error_message = "Invalid order total. Please add items to your order.";
					break;
				case 'database_error':
					$error_message = "Database error occurred. Please try again.";
					break;
				default:
					$error_message = "An error occurred while processing your order.";
			}
			echo '<div style="background-color: #ff6b6b; color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-weight: bold;">⚠️ ' . $error_message . '</div>';
		}
		
		// Display success message
		if(isset($_GET['success'])) {
			echo '<div style="background-color: #51cf66; color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-weight: bold;">✅ Order placed successfully!</div>';
		}
		?>
		
		<form action="insert_man_order.php" method="POST" class="form" id="orderForm">
		<table class="table_displayData">
			<tr>
				<th> ID </th>
				<th> Name </th>
				<th> Unit Price (৳) </th>
				<th> Available </th>
				<th> Quantity </th>
				<th> Total Price (৳) </th>
			</tr>
			<?php $i=1; while($row_selectProducts = mysqli_fetch_array($result_selectProducts)) { ?>
			<tr>
				<td> <?php echo $row_selectProducts['pro_id']; ?> </td>
				<td> <?php echo $row_selectProducts['pro_name']; ?> </td>
				<td> ৳<?php echo $row_selectProducts['pro_price']; ?> </td>
				<td> <?php echo $row_selectProducts['available_quantity']; ?> </td>
				<td> <input type="text" class="quantity" id="<?php echo $row_selectProducts['pro_id']; ?>" name="<?php echo "txtQuantity".$row_selectProducts['pro_id']; ?>" /> </td>
				<td> <div id="<?php echo "totalPrice".$row_selectProducts['pro_id']; ?>"></div> </td>
			</tr>
			<?php $i++; } ?>
			<tr>
				<td colspan="5" style="text-align:right;"> Total Price (৳): </td>
				<td> <input type="text" size="10" id="txtFinalAmount" name="total_price" readonly="readonly" value="" /> </td>
			</tr>
		</table>
		<input id="btnSubmit" type="submit" value="Post Order" class="submit_button" />
		</form>
	</section>
	<?php
		include("../includes/footer.inc.php");
	?>
	<script type="text/javascript" src="../includes/jquery.js"> </script>
	<script type="text/javascript" src="order_items.js"> </script>
	<script>
		// Add form validation
		$('#orderForm').submit(function(e) {
			var totalAmount = parseFloat($('#txtFinalAmount').val());
			if(isNaN(totalAmount) || totalAmount <= 0) {
				alert('Please add at least one item to your order before submitting.');
				e.preventDefault();
				return false;
			}
			
			// Check if at least one quantity field has a value
			var hasItems = false;
			$('.quantity').each(function() {
				if($(this).val() && parseFloat($(this).val()) > 0) {
					hasItems = true;
					return false; // Break the loop
				}
			});
			
			if(!hasItems) {
				alert('Please specify quantity for at least one product.');
				e.preventDefault();
				return false;
			}
			
			return true;
		});
	</script>
</body>
</html>