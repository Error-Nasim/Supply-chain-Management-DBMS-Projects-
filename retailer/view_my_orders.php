<?php
	require("../includes/config.php");
	include("../includes/validate_data.php");
	session_start();
	if(isset($_SESSION['retailer_login'])) {
		if($_SESSION['retailer_login'] == true && isset($_SESSION['retailer_id'])) {
			$retailer_id = $_SESSION['retailer_id'];
			$error = "";
			if($_SERVER['REQUEST_METHOD'] == "POST") {
				if(isset($_POST['cmbFilter'])) {
					if(!empty($_POST['txtId'])) {
						$result = validate_number($_POST['txtId']);
						if($result == 1) {
							$order_id = $_POST['txtId'];
							$query_selectOrder = "SELECT * FROM orders,retailer,area WHERE orders.retailer_id=retailer.retailer_id AND retailer.area_id=area.area_id AND order_id='$order_id' AND orders.retailer_id='$retailer_id'";
							$result_selectOrder = mysqli_query($con,$query_selectOrder);
							$row_selectOrder = mysqli_fetch_array($result_selectOrder);
							if(empty($row_selectOrder)){
							   $error = "* No order was found with this ID";
							}
							else {
								mysqli_data_seek($result_selectOrder,0);
							}
						}
						else {
							$error = "* Invalid ID";
						}
					}
					else if(!empty($_POST['txtDate'])) {
						$date = $_POST['txtDate'];
						$query_selectOrder = "SELECT * FROM orders,retailer,area WHERE orders.retailer_id=retailer.retailer_id AND retailer.area_id=area.area_id AND date='$date' AND orders.retailer_id='$retailer_id'";
						$result_selectOrder = mysqli_query($con,$query_selectOrder);
						$row_selectOrder = mysqli_fetch_array($result_selectOrder);
						if(empty($row_selectOrder)){
						   $error = "* No order was found with the selected Date";
						}
						else {
							mysqli_data_seek($result_selectOrder,0);
						}
						
					}
					else if(!empty($_POST['cmbStatus'])) {
						if($_POST['cmbStatus'] == "zero") {
							$status = 0;
						}
						else {
							$status = $_POST['cmbStatus'];
						}
						$query_selectOrder = "SELECT * FROM orders,retailer,area WHERE orders.retailer_id=retailer.retailer_id AND retailer.area_id=area.area_id AND status='$status' AND orders.retailer_id='$retailer_id' ORDER BY CASE WHEN status = 2 THEN 1 ELSE 0 END, approved, order_id DESC";
						$result_selectOrder = mysqli_query($con,$query_selectOrder);
						$row_selectOrder = mysqli_fetch_array($result_selectOrder);
						if(empty($row_selectOrder)){
						   $error = "* No order was found";
						}
						else {
							mysqli_data_seek($result_selectOrder,0);
						}
					}
					else if(!empty($_POST['cmbApproved'])) {
						if($_POST['cmbApproved'] == "zero") {
							$approved = 0;
						}
						else {
							$approved = $_POST['cmbApproved'];
						}
						$query_selectOrder = "SELECT * FROM orders,retailer,area WHERE orders.retailer_id=retailer.retailer_id AND retailer.area_id=area.area_id AND approved='$approved' AND orders.retailer_id='$retailer_id' ORDER BY CASE WHEN status = 2 THEN 1 ELSE 0 END, order_id DESC";
						$result_selectOrder = mysqli_query($con,$query_selectOrder);
						$row_selectOrder = mysqli_fetch_array($result_selectOrder);
						if(empty($row_selectOrder)){
						   $error = "* No order was found";
						}
						else {
							mysqli_data_seek($result_selectOrder,0);
						}
					}
					else {
						$error = "* Please enter the data to search for.";
					}
				}
				else {
					$error = "Please choose an option to search for.";
				}
			}
			else {
				$query_selectOrder = "SELECT * FROM orders WHERE retailer_id='$retailer_id' ORDER BY CASE WHEN status = 2 THEN 1 ELSE 0 END, order_id DESC";
				$result_selectOrder = mysqli_query($con,$query_selectOrder);
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
	<title> View Orders </title>
	<link rel="stylesheet" href="../includes/main_style.css" >
	<link rel="stylesheet" href="css/smoothness/jquery-ui.css">
	<script type="text/javascript" src="../includes/jquery.js"> </script>
	<script src="js/jquery-ui.js"></script>
	<script>
  $(function() {
    $( "#datepicker" ).datepicker({
     changeMonth:true,
     changeYear:true,
     yearRange:"-100:+0",
     dateFormat:"yy-mm-dd"
  });
  });
  </script>
</head>
<body>
	<?php
		include("../includes/header.inc.php");
		include("../includes/nav_retailer.inc.php");
		include("../includes/aside_retailer.inc.php");
	?>
	<section>
		<h1>My Orders</h1>
		<form action="" method="POST" class="form">
			Search By: 
			<div class="input-box">
			<select name="cmbFilter" id="cmbFilter">
			<option value="" disabled selected>-- Search By --</option>
			<option value="id"> Id </option>
			<option value="date"> Date </option>
			<option value="status"> Status </option>
			<option value="approved"> Approval </option>
			</select>
			</div>
			
			<div class="input-box"> <input type="text" name="txtId" id="txtId" style="display:none;" /> </div>
			<div class="input-box"> <input type="text" id="datepicker" name="txtDate" style="display:none;"/> </div>
			<div class="input-box">
			<select name="cmbStatus" id="cmbStatus" style="display:none;">
				<option value="" disabled selected>-- Select Option --</option>
				<option value="zero"> Pending </option>
				<option value="1"> Completed </option>
			</select>
			</div>
			<div class="input-box">
			<select name="cmbApproved" id="cmbApproved" style="display:none;">
				<option value="" disabled selected>-- Select Option --</option>
				<option value="zero"> Not Approved </option>
				<option value="1"> Approved </option>
			</select>
			</div>
			
			<input type="submit" class="submit_button" value="Search" />
			<?php if(!empty($error)): ?>
				<span class="error_message"><?php echo $error; ?></span>
			<?php endif; ?>
		</form>
		
		<form action="" method="POST" class="form">
		<table class="table_displayData" style="margin-top:20px;">
			<tr>
				<th> Order ID </th>
				<th> Date </th>
				<th> Approved </th>
				<th> Status </th>
				<th> Details </th>
				<th> Cancel </th>
			</tr>
			<?php $i=1; while($row_selectOrder = mysqli_fetch_array($result_selectOrder)) { ?>
			<tr>
			
				<td> <?php echo $row_selectOrder['order_id']; ?> </td>
				
				<td> <?php echo date("d-m-Y",strtotime($row_selectOrder['date'])); ?> </td>
				<td>
					<?php
						if($row_selectOrder['approved'] == 0) {
							echo "Not Approved";
						}
						else {
							echo "Approved";
						}
					?>
				</td>
				<td>
					<?php
						if($row_selectOrder['status'] == 0) {
							echo "Pending";
						}
						elseif($row_selectOrder['status'] == 2) {
							echo '<span style="color: #dc3545; font-weight: bold;">Cancelled</span>';
						}
						else {
							echo "Completed";
						}
					?>
				</td>
				<td> <a href="view_order_items.php?id=<?php echo $row_selectOrder['order_id']; ?>">Details</a> </td>
				<td>
					<?php if($row_selectOrder['status'] == 0): ?>
						<button type="button" onclick="cancelOrder(<?php echo $row_selectOrder['order_id']; ?>, 'retailer')" 
								style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 11px;">
							🗑️ Cancel
						</button>
					<?php elseif($row_selectOrder['status'] == 2): ?>
						<span style="color: #6c757d; font-style: italic; font-size: 11px;">Cancelled</span>
					<?php endif; ?>
				</td>
			</tr>
			<?php $i++; } ?>
		</table>
		</form>
	</section>
	
	<!-- Cancel Order Modal -->
	<div id="cancelModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
		<div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 10px; width: 400px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
			<h3 style="color: #dc3545; margin-bottom: 20px; text-align: center;">⚠️ Cancel Order</h3>
			<p style="margin-bottom: 15px; text-align: center;">Are you sure you want to cancel this order?</p>
			<textarea id="cancelReason" placeholder="Enter cancellation reason (optional)" 
					  style="width: 100%; height: 80px; border: 1px solid #ddd; border-radius: 5px; padding: 10px; font-family: Arial, sans-serif; margin-bottom: 20px;"></textarea>
			<div style="text-align: center;">
				<button onclick="confirmCancel()" 
						style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; border: none; padding: 10px 20px; border-radius: 25px; cursor: pointer; font-weight: bold; margin-right: 10px;">
					✓ Confirm Cancel
				</button>
				<button onclick="closeCancelModal()" 
						style="background: linear-gradient(135deg, #6c757d 0%, #545b62 100%); color: white; border: none; padding: 10px 20px; border-radius: 25px; cursor: pointer; font-weight: bold;">
					✕ Close
				</button>
			</div>
		</div>
	</div>
	
	<?php
		include("../includes/footer.inc.php");
	?>
	<script type="text/javascript">
		$('#cmbFilter').change(function() {
			var selected = $(this).val();
			if(selected == "id"){
				$('#txtId').show();
				$('#datepicker').hide();
				$('#cmbStatus').hide();
				$('#cmbApproved').hide();
			}
			else if (selected == "date"){
				$('#txtId').hide();
				$('#datepicker').show();
				$('#cmbStatus').hide();
				$('#cmbApproved').hide();
			}
			else if (selected == "status"){
				$('#txtId').hide();
				$('#datepicker').hide();
				$('#cmbStatus').show();
				$('#cmbApproved').hide();
			}
			else if (selected == "approved"){
				$('#txtId').hide();
				$('#datepicker').hide();
				$('#cmbStatus').hide();
				$('#cmbApproved').show();
			}
		});
	</script>
	
	<script type="text/javascript">
		var currentOrderId = null;
		var currentUserType = null;
		
		function cancelOrder(orderId, userType) {
			console.log('cancelOrder called - Order ID:', orderId, 'User Type:', userType);
			currentOrderId = orderId;
			currentUserType = userType;
			document.getElementById('cancelModal').style.display = 'block';
		}
		
		function closeCancelModal() {
			document.getElementById('cancelModal').style.display = 'none';
			document.getElementById('cancelReason').value = '';
			currentOrderId = null;
			currentUserType = null;
		}
		
		function confirmCancel() {
			console.log('confirmCancel called');
			console.log('Order ID:', currentOrderId, 'User Type:', currentUserType);
			
			if (!currentOrderId || !currentUserType) {
				alert('Error: Missing order information');
				return;
			}
			
			var reason = document.getElementById('cancelReason').value;
			console.log('Cancellation reason:', reason);
			
			// Show loading
			var modal = document.getElementById('cancelModal');
			modal.innerHTML = '<div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: #333;"><h3>Cancelling Order...</h3><p>Please wait...</p></div>';
			
			console.log('Sending AJAX request...');
			
			// Send AJAX request
			$.ajax({
				url: '../cancel_order.php',
				type: 'POST',
				data: {
					order_id: currentOrderId,
					user_type: currentUserType,
					reason: reason
				},
				dataType: 'json',
				success: function(response) {
					console.log('AJAX Success:', response);
					if (response.success) {
						alert('✅ ' + response.message);
						location.reload();
					} else {
						alert('❌ ' + response.message);
						closeCancelModal();
					}
				},
				error: function(xhr, status, error) {
					console.error('AJAX Error!');
					console.error('Status:', status);
					console.error('Error:', error);
					console.error('Response Text:', xhr.responseText);
					alert('❌ Error occurred while cancelling order. Check console for details.');
					closeCancelModal();
				}
			});
		}
	</script>
</body>
</html>