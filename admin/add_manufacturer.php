<?php
	include("../includes/config.php");
	include("../includes/validate_data.php");
	session_start();
	if(isset($_SESSION['admin_login'])) {
		if($_SESSION['admin_login'] == true) {
			$name = $email = $phone = $address = $username = $password = "";
			$nameErr = $emailErr = $phoneErr = $usernameErr = $passwordErr = $requireErr = $confirmMessage = "";
			$nameHolder = $emailHolder = $phoneHolder = $addressHolder = $usernameHolder = "";
			if($_SERVER['REQUEST_METHOD'] == "POST") {
				if(!empty($_POST['txtManufacturerName'])) {
					$nameHolder = $_POST['txtManufacturerName'];
					$resultValidate_name = validate_name($_POST['txtManufacturerName']);
					if($resultValidate_name == 1) {
						$name = $_POST['txtManufacturerName'];
					}
					else{
						$nameErr = $resultValidate_name;
					}
				}
				if(!empty($_POST['txtManufacturerEmail'])) {
					$emailHolder = $_POST['txtManufacturerEmail'];
					$resultValidate_email = validate_email($_POST['txtManufacturerEmail']);
					if($resultValidate_email == 1) {
						$email = $_POST['txtManufacturerEmail'];
					}
					else {
						$emailErr = $resultValidate_email;
					}
				}
				if(!empty($_POST['txtManufacturerPhone'])) {
					$phoneHolder = $_POST['txtManufacturerPhone'];
					$resultValidate_phone = validate_phone($_POST['txtManufacturerPhone']);
					if($resultValidate_phone == 1) {
						$phone = $_POST['txtManufacturerPhone'];
					}
					else {
						$phoneErr = $resultValidate_phone;
					}
				}
				if(!empty($_POST['txtManufacturerAddress'])) {
					$address = $_POST['txtManufacturerAddress'];
					$addressHolder = $_POST['txtManufacturerAddress'];
				}
if(!empty($_POST['txtManufacturerUname'])) {
$usernameHolder = $_POST['txtManufacturerUname'];
$resultValidate_username = validate_username($_POST['txtManufacturerUname']);
if($resultValidate_username == 1) {
// Check if username already exists
$check_username = mysqli_real_escape_string($con, $_POST['txtManufacturerUname']);
$query_checkUsername = "SELECT username FROM retailer WHERE username='$check_username' UNION SELECT username FROM manufacturer WHERE username='$check_username' UNION SELECT username FROM admin WHERE username='$check_username'";
$result_checkUsername = mysqli_query($con, $query_checkUsername);
if(mysqli_num_rows($result_checkUsername) > 0) {
$usernameErr = "* Username already exists. Please choose a different username.";
}
else {
$username = $_POST['txtManufacturerUname'];
}
}
else{
$usernameErr = $resultValidate_username;
}
}
				if(!empty($_POST['txtManufacturerPassword'])) {
					$resultValidate_password = validate_password($_POST['txtManufacturerPassword']);
					if($resultValidate_password == 1) {
						$password = $_POST['txtManufacturerPassword'];
					}
					else {
						$passwordErr = $resultValidate_password;
					}
				}
				if($name != null && $email != null && $username != null && $password != null) {
					$query_addManufacturer = "INSERT INTO manufacturer(man_name,man_email,man_phone,man_address,username,password) VALUES('$name','$email','$phone','$address','$username','$password')";
					if(mysqli_query($con,$query_addManufacturer)) {
						echo "<script> alert(\"Manufacturer Added Successfully\"); </script>";
						header('Refresh:0');
					}
					else {
						$requireErr = "Adding Manufacturer Failed: " . mysqli_error($con);
					}
				}
				else {
					$requireErr = "* Valid Name, Email, Username & Password are compulsory";
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
	<title> Add Manufacturer </title>
	<link rel="stylesheet" href="../includes/main_style.css" >
</head>
<body>
	<?php
		include("../includes/header.inc.php");
		include("../includes/nav_admin.inc.php");
		include("../includes/aside_admin.inc.php");
	?>
	<section>
		<h1>Add Manufacturer</h1>
		<form action="" method="POST" class="form">
		<ul class="form-list">
		<li>
			<div class="label-block"> <label for="manufacturer:name">Name</label> </div>
			<div class="input-box"> <input type="text" id="manufacturer:name" name="txtManufacturerName" placeholder="e.g., Square Pharmaceuticals, ACI Limited" value="<?php echo $nameHolder; ?>" required /> </div> <span class="error_message"><?php echo $nameErr; ?></span>
		</li>
		<li>
			<div class="label-block"> <label for="manufacturer:email">Email</label> </div>
			<div class="input-box"> <input type="text" id="manufacturer:email" name="txtManufacturerEmail" placeholder="e.g., info@company.com.bd" value="<?php echo $emailHolder; ?>" required /> </div> <span class="error_message"><?php echo $emailErr; ?></span>
		</li>
		<li>
			<div class="label-block"> <label for="manufacturer:phone">Phone</label> </div>
			<div class="input-box"> <input type="text" id="manufacturer:phone" name="txtManufacturerPhone" placeholder="e.g., 01712345678 or 02-9876543" value="<?php echo $phoneHolder; ?>" /> </div> <span class="error_message"><?php echo $phoneErr; ?></span>
		</li>
		<li>
			<div class="label-block"> <label for="manufacturer:address">Address</label> </div>
			<div class="input-box"> <textarea id="manufacturer:address" name="txtManufacturerAddress" placeholder="e.g., BSCIC Industrial Estate, Tongi, Gazipur"><?php echo $addressHolder; ?></textarea> </div>
		</li>
		<li>
			<div class="label-block"> <label for="manufacturer:username">Username</label> </div>
			<div class="input-box"> <input type="text" id="manufacturer:username" name="txtManufacturerUname" placeholder="e.g., square_pharma, aci_ltd" value="<?php echo $usernameHolder; ?>" required /> </div> <span class="error_message"><?php echo $usernameErr; ?></span>
		</li>
		<li>
			<div class="label-block"> <label for="manufacturer:password">Password</label> </div>
			<div class="input-box"> <input type="password" id="manufacturer:password" name="txtManufacturerPassword" placeholder="Password" required /> </div> <span class="error_message"><?php echo $passwordErr; ?></span>
		</li>
		<li>
			<input type="submit" value="Add Manufacturer" class="submit_button" />
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