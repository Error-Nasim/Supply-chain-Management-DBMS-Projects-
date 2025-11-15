<?php
	include("../includes/config.php");
	session_start();
	if(isset($_SESSION['retailer_login'])) {
			// Show ALL products with total available quantity from all manufacturers
			$query_selectProducts = "SELECT p.*, u.unit_name, 
									COALESCE(SUM(ms.quantity), 0) as available_quantity
									FROM products p 
									JOIN units u ON p.unit_id = u.unit_id 
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
	<title> View Products </title>
	<link rel="stylesheet" href="../includes/main_style.css" >
</head>
<body>
	<?php
		include("../includes/header.inc.php");
		include("../includes/nav_retailer.inc.php");
		include("../includes/aside_retailer.inc.php");
	?>
	<section>
		<h1>View Products</h1>
		<form action="" method="POST" class="form">
		<table class="table_displayData">
			<tr>
				<th> ID </th>
				<th> Name </th>
				<th> Price </th>
				<th> Unit </th>
				<th> Available Quantity </th>
			</tr>
			<?php $i=1; while($row_selectProducts = mysqli_fetch_array($result_selectProducts)) { ?>
			<tr>
				<td> <?php echo $row_selectProducts['pro_id']; ?> </td>
				<td> <?php echo $row_selectProducts['pro_name']; ?> </td>
				<td> ৳<?php echo number_format($row_selectProducts['pro_price'], 2); ?> </td>
				<td> <?php echo $row_selectProducts['unit_name']; ?> </td>
				<td> <strong><?php echo $row_selectProducts['available_quantity']; ?></strong> </td>
			</tr>
			<?php $i++; } ?>
		</table>
		</form>
	</section>
	<?php
		include("../includes/footer.inc.php");
	?>
</body>
</html>