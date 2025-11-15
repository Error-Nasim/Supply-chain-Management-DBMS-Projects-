<?php

	
	include("../includes/config.php");
	include("../includes/validate_data.php");
	session_start();
	if(isset($_SESSION['admin_login'])) {
		if($_SESSION['admin_login'] == true) {
			$name = $username = $password = $areacode = $phone = $email = $address = "";
			$nameErr = $usernameErr = $passwordErr = $phoneErr = $emailErr = $requireErr = $confirmMessage = "";
			$nameHolder = $usernameHolder = $phoneHolder = $emailHolder = $addressHolder = "";
			$query_selectArea = "SELECT * FROM area";
			$result_selectArea = mysqli_query($con,$query_selectArea);
			if($_SERVER['REQUEST_METHOD'] == "POST") {
				

			if(!empty($_POST["txtRetailerName"])) {
				$nameHolder = $_POST["txtRetailerName"];
				$name = $_POST["txtRetailerName"];
			}

if(!empty($_POST['txtRetailerUname'])) {
$usernameHolder = $_POST['txtRetailerUname'];
$resultValidate_username = validate_username($_POST['txtRetailerUname']);
if($resultValidate_username == 1) {
// Check if username already exists
$check_username = mysqli_real_escape_string($con, $_POST['txtRetailerUname']);
$query_checkUsername = "SELECT username FROM retailer WHERE username='$check_username' UNION SELECT username FROM manufacturer WHERE username='$check_username' UNION SELECT username FROM admin WHERE username='$check_username'";
$result_checkUsername = mysqli_query($con, $query_checkUsername);
if(mysqli_num_rows($result_checkUsername) > 0) {
$usernameErr = "* Username already exists. Please choose a different username.";
}
else {
$username = $_POST['txtRetailerUname'];
}
}
else{
$usernameErr = $resultValidate_username;
}
}
				if(!empty($_POST['txtRetailerPassword'])) {
					$resultValidate_password = validate_password($_POST['txtRetailerPassword']);
					if($resultValidate_password == 1) {
						$password = $_POST['txtRetailerPassword'];
					}
					else {
						$passwordErr = $resultValidate_password;
					}
				}
				if(!empty($_POST['cmbAreaCode'])) {
					$areacode = $_POST['cmbAreaCode'];
				}
				if(!empty($_POST['txtRetailerPhone'])) {
					$phoneHolder = $_POST['txtRetailerPhone'];
					$resultValidate_phone = validate_phone($_POST['txtRetailerPhone']);
					if($resultValidate_phone == 1) {
						$phone = $_POST['txtRetailerPhone'];
					}
					else {
						$phoneErr = $resultValidate_phone;
					}
				}
				if(!empty($_POST['txtRetailerEmail'])) {
					$emailHolder = $_POST['txtRetailerEmail'];
					$resultValidate_email = validate_email($_POST['txtRetailerEmail']);
					if($resultValidate_email == 1) {
						$email = $_POST['txtRetailerEmail'];
					}
					else {
						$emailErr = $resultValidate_email;
					}
				}
			if(!empty($_POST['txtRetailerAddress'])) {
				$address = $_POST['txtRetailerAddress'];
				$addressHolder = $_POST['txtRetailerAddress'];
			}
			if($name != null && $username != null && $password != null && $areacode != null && $email != null) {
				// Set default values for optional fields if empty
				if(empty($phone)) {
					$phone = 'N/A';
				}
				if(empty($address)) {
					$address = 'N/A';
				}
				$query_addRetailer = "INSERT INTO retailer(retailer_name,retailer_email,retailer_phone,retailer_address,area_id,username,password) VALUES('$name','$email','$phone','$address','$areacode','$username','$password')";
				if(mysqli_query($con,$query_addRetailer)) {
						echo "<script> alert(\"Retailer Added Successfully\"); </script>";
						header('Refresh:0');
					}
					else {
						$requireErr = "Adding Retailer Failed: " . mysqli_error($con);
					}
				}
				else {
					$requireErr = "* Valid Name, Username, Password, Area & Email are compulsory";
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
	<title> Add Retailer </title>
	<link rel="stylesheet" href="../includes/main_style.css" >
</head>
<body>
	<?php
		include("../includes/header.inc.php");
		include("../includes/nav_admin.inc.php");
		include("../includes/aside_admin.inc.php");
	?>
	<section>
		<h1>Add Retailer</h1>
		<form action="" method="POST" class="form">
		<ul class="form-list">
	<li>
		<div class="label-block"> <label for="retailer:name">Retailer Name</label> </div>
		<div class="input-box"> <input type="text" id="retailer:name" name="txtRetailerName" placeholder="e.g., City Shopping Complex, Rahman Store" value="<?php echo $nameHolder; ?>" required /> </div> <span class="error_message"><?php echo $nameErr; ?></span>
	</li>
		<li>
			<div class="label-block"> <label for="retailer:username">Username</label> </div>
			<div class="input-box"> <input type="text" id="retailer:username" name="txtRetailerUname" placeholder="e.g., rahman_shop, alam_store" value="<?php echo $usernameHolder; ?>" required /> </div> <span class="error_message"><?php echo $usernameErr; ?></span>
		</li>
		<li>
			<div class="label-block"> <label for="retailer:password">Password</label> </div>
			<div class="input-box"> <input type="password" id="retailer:password" name="txtRetailerPassword" placeholder="Enter secure password" required /> </div> <span class="error_message"><?php echo $passwordErr; ?></span>
		</li>
		<li>
			<div class="label-block"> <label for="retailer:areaCode">Area Code</label> </div>
			<div class="input-box">
				<select name="cmbAreaCode" id="retailer:areaCode">
					<option value="" disabled selected>--- Select Area Code ---</option>
			<?php while($row_selectArea = mysqli_fetch_array($result_selectArea)) { ?>
			<option value="<?php echo $row_selectArea["area_id"]; ?>"><?php echo $row_selectArea["area_code"]." (".$row_selectArea["area_name"].")"; ?></option>
			<?php } ?>
				</select>
			 </div>
		</li>
		<li>
			<div class="label-block"> <label for="retailer:phone">Phone</label> </div>
			<div class="input-box"> <input type="text" id="retailer:phone" name="txtRetailerPhone" placeholder="e.g., 01712345678 or 02-9876543" value="<?php echo $phoneHolder; ?>" /> </div> <span class="error_message"><?php echo $phoneErr; ?></span>
		</li>
		<li>
			<div class="label-block"> <label for="retailer:email">Email</label> </div>
			<div class="input-box"> <input type="text" id="retailer:email" name="txtRetailerEmail" placeholder="e.g., retailer@example.com" value="<?php echo $emailHolder; ?>" required /> </div> <span class="error_message"><?php echo $emailErr; ?></span>
		</li>
		<li>
			<div class="label-block"> <label for="retailer:address">Address</label> </div>
			<div class="input-box"> <textarea type="text" id="retailer:address" name="txtRetailerAddress" placeholder="e.g., 123 Dhanmondi Road, Dhaka-1205"><?php echo $addressHolder; ?></textarea> </div>
		</li>
		<li>
			<input type="submit" value="Add Retailer" class="submit_button" />
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