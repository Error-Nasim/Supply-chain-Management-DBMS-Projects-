<?php
include("../includes/config.php");
session_start();
if(isset($_SESSION['admin_login'])) {
if($_SESSION['admin_login'] == true) {
$query_selectRetailer = "SELECT * FROM retailer,area WHERE retailer.area_id=area.area_id";
$result_selectRetailer = mysqli_query($con,$query_selectRetailer);
if($_SERVER['REQUEST_METHOD'] == "POST") {
if(isset($_POST['chkId'])) {
$chkId = $_POST['chkId'];
$success_count = 0;
$error_count = 0;
$error_messages = array();

foreach($chkId as $id) {
$query_checkOrders = "SELECT COUNT(*) as order_count FROM orders WHERE retailer_id='$id'";
$result_check = mysqli_query($con, $query_checkOrders);
$row_check = mysqli_fetch_array($result_check);

if($row_check['order_count'] > 0) {
$query_getName = "SELECT retailer_name FROM retailer WHERE retailer_id='$id'";
$result_name = mysqli_query($con, $query_getName);
$row_name = mysqli_fetch_array($result_name);
$error_messages[] = $row_name['retailer_name'] . " has " . $row_check['order_count'] . " order(s)";
$error_count++;
} else {
$query_deleteRetailer = "DELETE FROM retailer WHERE retailer_id='$id'";
if(mysqli_query($con, $query_deleteRetailer)) {
$success_count++;
} else {
$error_count++;
}
}
}

if($success_count > 0) {
echo "<script> alert('Successfully deleted $success_count retailer(s)'); </script>";
}
if($error_count > 0) {
$error_msg = "Cannot delete $error_count retailer(s):\\n";
foreach($error_messages as $msg) {
$error_msg .= "- $msg\\n";
}
echo "<script> alert('$error_msg'); </script>";
}
header('Refresh:0');
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
<title> View Retailer </title>
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
include("../includes/nav_admin.inc.php");
include("../includes/aside_admin.inc.php");
?>
<section>
<h1>View Retailer</h1>
<form action="" method="POST" class="form">
<table class="table_displayData">
<tr>
<th> <input type="checkbox" onClick="toggle(this)" /> </th>
<th>Sr. No.</th>
<th>Username</th>
<th>Area Code</th>
<th>Phone</th>
<th>Email</th>
<th>Address</th>
<th> Edit </th>
</tr>
<?php $i=1; while($row_selectRetailer = mysqli_fetch_array($result_selectRetailer)) { ?>
<tr>
<td> <input type="checkbox" name="chkId[]" value="<?php echo $row_selectRetailer['retailer_id']; ?>" /> </td>
<td> <?php echo $i; ?> </td>
<td> <?php echo $row_selectRetailer['username']; ?> </td>
<td> <?php echo $row_selectRetailer['area_code']; ?> </td>
<td> <?php echo $row_selectRetailer['retailer_phone']; ?> </td>
<td> <?php echo $row_selectRetailer['retailer_email']; ?> </td>
<td> <?php echo $row_selectRetailer['retailer_address']; ?> </td>
<td> <a href="edit_retailer.php?id=<?php echo $row_selectRetailer['retailer_id']; ?>"><img src="../images/edit.png" alt="edit" /></a> </td>
</tr>
<?php $i++; } ?>
</table>
<input type="submit" value="Delete" class="submit_button"/>
</form>
</section>
<?php
include("../includes/footer.inc.php");
?>
</body>
</html>
