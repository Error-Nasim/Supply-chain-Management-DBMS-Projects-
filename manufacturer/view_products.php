<?php
	include("../includes/config.php");
	session_start();
	if(isset($_SESSION['manufacturer_login'])) {
		if($_SESSION['manufacturer_login'] == true) {
			$manufacturer_id = $_SESSION['manufacturer_id'];
			// Show ALL products with manufacturer's own stock quantity
			$query_selectProducts = "SELECT p.*, u.unit_name, COALESCE(ms.quantity, 0) as my_quantity
									FROM products p 
									LEFT JOIN units u ON p.unit_id = u.unit_id 
									LEFT JOIN manufacturer_stock ms ON p.pro_id = ms.product_id AND ms.manufacturer_id = '$manufacturer_id'
									ORDER BY p.pro_name";
			$result_selectProducts = mysqli_query($con,$query_selectProducts);
		}
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
	<script language="JavaScript">
	function toggle(source) {
		checkboxes = document.getElementsByName('chkId[]');
		for(var i=0, n=checkboxes.length;i<n;i++) {
			checkboxes[i].checked = source.checked;
		}
	}
	</script>
</head>
<body>
	<?php
		include("../includes/header.inc.php");
		include("../includes/nav_manufacturer.inc.php");
		include("../includes/aside_manufacturer.inc.php");
	?>
	<section>
		<h1>View Products</h1>
		<table class="table_displayData">
			<tr>
				<th> Code </th>
				<th> Name </th>
				<th> Price </th>
				<th> Unit </th>
				<th> My Quantity </th>
			</tr>
			<?php $i=1; while($row_selectProducts = mysqli_fetch_array($result_selectProducts)) { ?>
		<tr>
			<td> <?php echo $row_selectProducts['pro_id']; ?> </td>
			<td> <?php echo $row_selectProducts['pro_name']; ?> </td>
			<td> ৳<?php echo $row_selectProducts['pro_price']; ?> </td>
		<td> <?php echo $row_selectProducts['unit_name']; ?> </td>
		<td> <?php echo $row_selectProducts['my_quantity']; ?> </td>
		</tr>
			<?php $i++; } ?>
		</table>
	</section>
	<?php
		include("../includes/footer.inc.php");
	?>
</body>
</html>