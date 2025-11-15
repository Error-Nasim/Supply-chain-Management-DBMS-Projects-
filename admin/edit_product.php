<?php
	include("../includes/config.php");
	include("../includes/validate_data.php");
	session_start();
	if(isset($_SESSION['admin_login'])) {
		if($_SESSION['admin_login'] == true) {
			$id = $_GET['id'];
			$query_selectProductDetails = "SELECT * FROM products WHERE pro_id='$id'";
			$result_selectProductDetails = mysqli_query($con,$query_selectProductDetails);
			$row_selectProductDetails = mysqli_fetch_array($result_selectProductDetails);
			$query_selectUnit = "SELECT unit_id,unit_name FROM units";
			$result_selectUnit = mysqli_query($con,$query_selectUnit);
			$name = $price = $unit = $description = "";
			$nameErr = $priceErr = $requireErr = $confirmMessage = "";
			$nameHolder = $priceHolder = $descriptionHolder = "";
			if($_SERVER['REQUEST_METHOD'] == "POST") {
				if(!empty($_POST['txtProductName'])) {
					$nameHolder = $_POST['txtProductName'];
					$name = $_POST['txtProductName'];
				}
				if(!empty($_POST['txtProductPrice'])) {
					$priceHolder = $_POST['txtProductPrice'];
					$resultValidate_price = validate_price($_POST['txtProductPrice']);
					if($resultValidate_price == 1) {
						$price = $_POST['txtProductPrice'];
					}
					else {
						$priceErr = $resultValidate_price;
					}
				}
				if(isset($_POST['cmbProductUnit'])) {
					$unit = $_POST['cmbProductUnit'];
				}
				if(isset($_POST['cmbProductCategory'])) {
					$_POST['cmbProductCategory'];
				}
				if(empty($_POST['rdbStock'])) {
					$rdbStock = "";
				}
				else {
					if($_POST['rdbStock'] == 1) {
						$rdbStock = 1;
					}
					else if($_POST['rdbStock'] == 2) {
						$rdbStock = 2;
					}
				}
				if(!empty($_POST['txtProductDescription'])) {
					$description = $_POST['txtProductDescription'];
					$descriptionHolder = $_POST['txtProductDescription'];
				}
				if($name != null && $price != null && $unit != null && $rdbStock == 1) {
					$rdbStock = 0;
					$query_UpdateProduct = "UPDATE products SET pro_name='$name',pro_desc='$description',pro_price='$price',unit_id='$unit'='$category',quantity='$rdbStock' WHERE pro_id='$id'";
					if(mysqli_query($con,$query_UpdateProduct)) {
						echo "<script> alert(\"Product Updated Successfully\"); </script>";
						header('Refresh:0;url=view_products.php');
					}
					else {
						$requireErr = "Updating Product Failed";
					}
				}
				else if($name != null && $price != null && $unit != null && $rdbStock == 2) {
						$query_UpdateProduct = "UPDATE products SET pro_name='$name',pro_desc='$description',pro_price='$price',unit_id='$unit'='$category',quantity=NULL WHERE pro_id='$id'";
					if(mysqli_query($con,$query_UpdateProduct)) {
						echo "<script> alert(\"Product Updated Successfully\"); </script>";
						header('Refresh:0');
					}
					else {
						$requireErr = "Updating Product Failed";
					}
				}
				else {
					$requireErr = "* All Fields are Compulsory with valid values except Description";
				}
			}
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
	<title> Add Product </title>
	<link rel="stylesheet" href="../includes/main_style.css" >
</head>
<body>
	<?php
		include("../includes/header.inc.php");
		include("../includes/nav_admin.inc.php");
		include("../includes/aside_admin.inc.php");
	?>
	<section>
		<h1>Edit Product</h1>
		<form action="" method="POST" class="form">
		<ul class="form-list">
		<li>
			<div class="label-block"> <label for="product:name">Product Name</label> </div>
			<div class="input-box"> <input type="text" id="product:name" name="txtProductName" placeholder="e.g., Basmati Rice, Bengal Gram" value="<?php echo $row_selectProductDetails['pro_name']; ?>" required /> </div> <span class="error_message"><?php echo $nameErr; ?></span>
		</li>
		<li>
			<div class="label-block"> <label for="product:price">Price (৳ Taka)</label> </div>
			<div class="input-box"> <input type="text" id="product:price" name="txtProductPrice" placeholder="e.g., 120.50 (in Taka)" value="<?php echo $row_selectProductDetails['pro_price']; ?>" required /> </div> <span class="error_message"><?php echo $priceErr; ?></span>
		</li>
		<li>
		<div class="label-block"> <label for="product:unit">Unit Type</label> </div>
		<div class="input-box">
		<select name="cmbProductUnit" id="product:unit">
			<option value="" disabled selected>--- Select Unit ---</option>
			<?php while($row_selectUnit = mysqli_fetch_array($result_selectUnit)) { ?>
			<option value="<?php echo $row_selectUnit["unit_id"]; ?>" <?php if($row_selectProductDetails['unit_id'] == $row_selectUnit["unit_id"]){echo "selected";} ?>> <?php echo $row_selectUnit["unit_name"]; ?> </option>
			<?php } ?>
		</select>
		</div>
		</li>
		<li>
		<div class="label-block"> <label for="product:category">Category</label> </div>
		<div class="input-box">
		<select name="cmbProductCategory" id="product:category">
			<option value="" disabled selected>--- Select Category ---</option>
			<?php while($row_selectCategory = mysqli_fetch_array($result_selectCategory)) { ?>
			<option value="<?php echo $row_selectCategory["cat_id"]; ?>" <?php if($row_selectProductDetails['cat_id'] == $row_selectCategory["cat_id"]){echo "selected";} ?>> <?php echo $row_selectCategory["cat_name"]; ?> </option>
			<?php } ?>
		</select>
		</div>
		</li>
		<li>
			<div class="label-block"> <label for="product:stock">Stock Management</label> </div>
			<input type="radio" name="rdbStock" value="1">Enable
			<input type="radio" name="rdbStock" value="2">Disable
		</li>
		<li>
			<div class="label-block"> <label for="product:description">Description</label> </div>
			<div class="input-box"> <textarea type="text" id="product:description" name="txtProductDescription" placeholder="Description"><?php echo $row_selectProductDetails['pro_desc']; ?></textarea> </div>
		</li>
		<li>
			<input type="submit" value="Update Product" class="submit_button" />
			<?php if(!empty($requireErr)): ?>
				<span class="error_message"><?php echo $requireErr; ?></span>
			<?php endif; ?>
			<?php if(!empty($confirmMessage)): ?>
				<span class="confirm_message"><?php echo $confirmMessage; ?></span>
			<?php endif; ?>
		</li>
		</ul>
		</form>
	</section>
	<?php
		include("../includes/footer.inc.php");
	?>
</body>
</html>