<?php
	include("../includes/config.php");
	session_start();
	if(isset($_SESSION['manufacturer_login'])) {
		if($_SESSION['manufacturer_login'] == true) {
			$query_selectUnit = "SELECT * FROM units";
			$result_selectUnit = mysqli_query($con,$query_selectUnit);
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
	<title> View Units </title>
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
		<h1>View Units</h1>
		<table class="table_displayData">
			<tr>
				<th>Sr. No.</th>
				<th>Name</th>
				<th>Description</th>
			</tr>
			<?php $i=1; while($row_selectUnit = mysqli_fetch_array($result_selectUnit)) { ?>
			<tr>
				<td> <?php echo $i; ?> </td>
				<td> <?php echo $row_selectUnit['unit_name']; ?> </td>
				<td> <?php echo $row_selectUnit['unit_details']; ?> </td>
			</tr>
			<?php $i++; } ?>
		</table>
	</section>
	<?php
		include("../includes/footer.inc.php");
	?>
</body>
</html>