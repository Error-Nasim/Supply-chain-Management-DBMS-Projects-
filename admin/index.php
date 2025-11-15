<?php
	include("../includes/config.php");
	session_start();
	if(isset($_SESSION['admin_login'])) {
		if($_SESSION['admin_login'] == true) {
			//select last 5 retialers
			$query_selectRetailer = "SELECT r.*, a.area_name, a.area_code 
									FROM retailer r 
									JOIN area a ON r.area_id = a.area_id 
									ORDER BY r.retailer_id DESC LIMIT 5";
			$result_selectRetailer = mysqli_query($con,$query_selectRetailer);
			if (!$result_selectRetailer) {
				echo "Error: " . mysqli_error($con);
				$result_selectRetailer = false; // Prevent the while loop from running
			}
			//select last 5 manufacturers
			$query_selectManufacturer = "SELECT * FROM manufacturer ORDER BY man_id DESC LIMIT 5";
			$result_selectManufacturer = mysqli_query($con,$query_selectManufacturer);
			if (!$result_selectManufacturer) {
				echo "Error: " . mysqli_error($con);
				$result_selectManufacturer = false;
			}
			//select last 5 products
			$query_selectProducts = "SELECT p.*, u.unit_name FROM products p 
									JOIN units u ON p.unit_id = u.unit_id 
									ORDER BY p.pro_id DESC LIMIT 5";
			$result_selectProducts = mysqli_query($con,$query_selectProducts);
			if (!$result_selectProducts) {
				echo "Error: " . mysqli_error($con);
				$result_selectProducts = false;
			}
		}
		else {
			header('Location:../index.php');
			exit();
		}
	}
	else {
		header('Location:../index.php');
		exit();
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title> Admin: Home </title>
	<link rel="stylesheet" href="../includes/main_style.css" >
</head>
<body>
	<?php
		include("../includes/header.inc.php");
		include("../includes/nav_admin.inc.php");
		include("../includes/aside_admin.inc.php");
	?>
	<section>
		<h1>Welcome Admin</h1>
		<article>
			<h2>Recently Added Retialers</h2>
			<table class="table_displayData">
				<tr>
					<th>Sr. No.</th>
					<th>Username</th>
					<th>Area Code</th>
					<th>Phone</th>
					<th>Email</th>
					<th>Address</th>
				</tr>
				<?php 
				if($result_selectRetailer && mysqli_num_rows($result_selectRetailer) > 0) {
					$i=1; 
					while($row_selectRetailer = mysqli_fetch_array($result_selectRetailer)) { 
				?>
				<tr>
					<td> <?php echo $i; ?> </td>
					<td> <?php echo $row_selectRetailer['retailer_name']; ?> </td>
					<td> <?php echo $row_selectRetailer['area_code']; ?> </td>
					<td> <?php echo isset($row_selectRetailer['retailer_phone']) ? $row_selectRetailer['retailer_phone'] : 'N/A'; ?> </td>
					<td> <?php echo isset($row_selectRetailer['retailer_email']) ? $row_selectRetailer['retailer_email'] : 'N/A'; ?> </td>
					<td> <?php echo isset($row_selectRetailer['retailer_address']) ? $row_selectRetailer['retailer_address'] : 'N/A'; ?> </td>
				</tr>
				<?php $i++; } 
				} else { ?>
					<tr><td colspan="6" style="text-align: center; padding: 20px; color: #6c757d;">No retailers found</td></tr>
				<?php } ?>
			</table>
		</article>
		
		<article>
			<h2>Recently Added Manufacturers</h2>
			<table class="table_displayData">
			<tr>
				<th>Sr. No.</th>
				<th>Name</th>
				<th>Email</th>
				<th>Phone</th>
				<th>Username</th>
			</tr>
			<?php 
			if($result_selectManufacturer && mysqli_num_rows($result_selectManufacturer) > 0) {
				$i=1; 
				while($row_selectManufacturer = mysqli_fetch_array($result_selectManufacturer)) { 
			?>
			<tr>
				<td> <?php echo $i; ?> </td>
				<td> <?php echo $row_selectManufacturer['man_name']; ?> </td>
				<td> <?php echo $row_selectManufacturer['man_email']; ?> </td>
				<td> <?php echo $row_selectManufacturer['man_phone']; ?> </td>
				<td> <?php echo $row_selectManufacturer['username']; ?> </td>
			</tr>
			<?php $i++; } 
			} else { ?>
				<tr><td colspan="5" style="text-align: center; padding: 20px; color: #6c757d;">No manufacturers found</td></tr>
			<?php } ?>
		</table>
		</article>
		
		<article>
			<h2>Recently Added Products</h2>
			<table class="table_displayData">
			<tr>
				<th> Code </th>
				<th> Name </th>
				<th> Price </th>
				<th> Unit </th>
				<th> Category </th>
				<th> Quantity </th>
			</tr>
			<?php 
			if($result_selectProducts && mysqli_num_rows($result_selectProducts) > 0) {
				$i=1; 
				while($row_selectProducts = mysqli_fetch_array($result_selectProducts)) { 
			?>
			<tr>
				<td> <?php echo $row_selectProducts['pro_id']; ?> </td>
				<td> <?php echo $row_selectProducts['pro_name']; ?> </td>
				<td> ৳<?php echo $row_selectProducts['pro_price']; ?> </td>
				<td> <?php echo $row_selectProducts['unit_name']; ?> </td>
				<td> <?php echo $row_selectProducts['cat_name']; ?> </td>
				<td> <?php if($row_selectProducts['quantity'] == NULL){ echo "N/A";} else {echo $row_selectProducts['quantity'];} ?> </td>
			</tr>
			<?php $i++; } 
			} else { ?>
				<tr><td colspan="4" style="text-align: center; padding: 20px; color: #6c757d;">No products found</td></tr>
			<?php } ?>
		</table>
		</article>
	</section>
	<?php
		include("../includes/footer.inc.php");
	?>
</body>
</html>