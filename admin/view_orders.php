<?php
	require("../includes/config.php");
	include("../includes/validate_data.php");
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	session_start();
	
	if(isset($_SESSION['admin_login'])) {
		$error = $success = "";
		
		// Handle order cancellation
		if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['cancel_order'])) {
			$order_id = intval($_POST['order_id']);
			$reason = mysqli_real_escape_string($con, $_POST['cancellation_reason']);
			
			if($order_id > 0) {
				$query_cancel = "UPDATE orders 
								SET status = 2, 
								    cancelled_by = 'admin',
								    cancelled_date = NOW(),
								    cancellation_reason = '$reason'
								WHERE order_id = '$order_id'";
				
				if(mysqli_query($con, $query_cancel)) {
					$success = "Order #$order_id has been cancelled successfully!";
				} else {
					$error = "Error cancelling order: " . mysqli_error($con);
				}
			}
		}
		
		// Get retailers for filter dropdown
		$querySelectRetailer = "SELECT r.*, a.area_name 
								FROM retailer r 
								JOIN area a ON r.area_id = a.area_id 
								ORDER BY r.retailer_name";
		$resultSelectRetailer = mysqli_query($con,$querySelectRetailer);
		
		// Handle assignment submission
	if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['assign_order'])) {
		$order_id = intval($_POST['order_id']);
		$manufacturer_id = intval($_POST['manufacturer_id']);
		
		if($order_id > 0 && $manufacturer_id > 0) {
			// Check if order was cancelled by admin - if so, prevent reassignment
			$query_checkCancel = "SELECT cancelled_by FROM orders WHERE order_id = '$order_id'";
			$result_checkCancel = mysqli_query($con, $query_checkCancel);
			$cancelData = mysqli_fetch_array($result_checkCancel);
			
			if($cancelData && $cancelData['cancelled_by'] == 'admin') {
				$error = "Cannot reassign orders cancelled by admin. Admin cancellations are permanent.";
			} else {
				// Reset cancelled status when reassigning, set approved=0 so manufacturer must confirm
				$query_assign = "UPDATE orders 
								SET assigned_manufacturer_id = '$manufacturer_id', 
								    approved = 0,
								    status = 0,
								    cancelled_by = NULL,
								    cancelled_date = NULL,
								    cancellation_reason = NULL
								WHERE order_id = '$order_id'";
				
				if(mysqli_query($con, $query_assign)) {
					$success = "Order #$order_id successfully assigned to manufacturer!";
				} else {
					$error = "Error assigning order: " . mysqli_error($con);
				}
			}
		}
	}		if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['cmbFilter'])) {
			if(!empty($_POST['txtId'])) {
				$result = validate_number($_POST['txtId']);
				if($result == 1) {
					$order_id = $_POST['txtId'];
					$query_selectOrder = "SELECT o.*, r.retailer_name, r.retailer_phone, a.area_name, a.area_code, m.man_name as manufacturer_name
										 FROM orders o
										 JOIN retailer r ON o.retailer_id = r.retailer_id 
										 JOIN area a ON r.area_id = a.area_id
										 LEFT JOIN manufacturer m ON o.assigned_manufacturer_id = m.man_id
										 WHERE o.order_id = '$order_id'";
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
					else if(!empty($_POST['cmbRetailer'])) {
						$retailer_id = $_POST['cmbRetailer'];
						$query_selectOrder = "SELECT o.*, r.retailer_name, r.retailer_phone, a.area_name, a.area_code, m.man_name as manufacturer_name
											 FROM orders o
											 JOIN retailer r ON o.retailer_id = r.retailer_id 
											 JOIN area a ON r.area_id = a.area_id
											 LEFT JOIN manufacturer m ON o.assigned_manufacturer_id = m.man_id
										 WHERE o.retailer_id = '$retailer_id' 
										 ORDER BY CASE WHEN o.cancelled_by IN ('admin', 'retailer') THEN 1 ELSE 0 END, o.assigned_manufacturer_id IS NULL DESC, o.approved, o.order_id DESC";
						$result_selectOrder = mysqli_query($con,$query_selectOrder);
						$row_selectOrder = mysqli_fetch_array($result_selectOrder);
						if(empty($row_selectOrder)){
						   $error = "* No order was found for this retailer";
						}
						else {
							mysqli_data_seek($result_selectOrder,0);
						}
					}
					else if(!empty($_POST['txtDate'])) {
						$date = $_POST['txtDate'];
						$query_selectOrder = "SELECT o.*, r.retailer_name, r.retailer_phone, a.area_name, a.area_code, m.man_name as manufacturer_name
											 FROM orders o
											 JOIN retailer r ON o.retailer_id = r.retailer_id 
											 JOIN area a ON r.area_id = a.area_id
											 LEFT JOIN manufacturer m ON o.assigned_manufacturer_id = m.man_id
										 WHERE o.date = '$date' 
										 ORDER BY CASE WHEN o.cancelled_by IN ('admin', 'retailer') THEN 1 ELSE 0 END, o.assigned_manufacturer_id IS NULL DESC, o.approved, o.order_id DESC";
						$result_selectOrder = mysqli_query($con,$query_selectOrder);
						$row_selectOrder = mysqli_fetch_array($result_selectOrder);
						if(empty($row_selectOrder)){
						   $error = "* No order was found for the selected Date";
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
						$query_selectOrder = "SELECT o.*, r.retailer_name, r.retailer_phone, a.area_name, a.area_code, m.man_name as manufacturer_name
											 FROM orders o
											 JOIN retailer r ON o.retailer_id = r.retailer_id 
											 JOIN area a ON r.area_id = a.area_id
											 LEFT JOIN manufacturer m ON o.assigned_manufacturer_id = m.man_id
										 WHERE o.status = '$status' 
										 ORDER BY CASE WHEN o.cancelled_by IN ('admin', 'retailer') THEN 1 ELSE 0 END, o.assigned_manufacturer_id IS NULL DESC, o.approved, o.order_id DESC";
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
						$query_selectOrder = "SELECT o.*, r.retailer_name, r.retailer_phone, a.area_name, a.area_code, m.man_name as manufacturer_name
											 FROM orders o
											 JOIN retailer r ON o.retailer_id = r.retailer_id 
											 JOIN area a ON r.area_id = a.area_id
											 LEFT JOIN manufacturer m ON o.assigned_manufacturer_id = m.man_id
										 WHERE o.approved = '$approved' 
										 ORDER BY CASE WHEN o.cancelled_by IN ('admin', 'retailer') THEN 1 ELSE 0 END, o.assigned_manufacturer_id IS NULL DESC, o.order_id DESC";
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
			// Default query - show all orders
			$query_selectOrder = "SELECT o.*, r.retailer_name, r.retailer_phone, a.area_name, a.area_code, m.man_name as manufacturer_name
								 FROM orders o
								 JOIN retailer r ON o.retailer_id = r.retailer_id 
								 JOIN area a ON r.area_id = a.area_id
							 LEFT JOIN manufacturer m ON o.assigned_manufacturer_id = m.man_id
							 ORDER BY CASE WHEN o.cancelled_by IN ('admin', 'retailer') THEN 1 ELSE 0 END, o.assigned_manufacturer_id IS NULL DESC, o.approved, o.order_id DESC";
		$result_selectOrder = mysqli_query($con,$query_selectOrder);
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
		include("../includes/nav_admin.inc.php");
		include("../includes/aside_admin.inc.php");
	?>
	<section>
		<h1>Orders</h1>
		
		<?php if(!empty($success)) { ?>
			<div style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid #28a745;">
				<strong>✅ Success:</strong> <?php echo $success; ?>
			</div>
		<?php } ?>
		
		<?php if(!empty($error)) { ?>
			<div style="background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%); color: #721c24; padding: 15px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid #dc3545;">
				<strong>❌ Error:</strong> <?php echo $error; ?>
			</div>
		<?php } ?>
		
		<form action="" method="POST" class="form">
			Search By: 
			<div class="input-box">
			<select name="cmbFilter" id="cmbFilter">
			<option value="" disabled selected>-- Search By --</option>
			<option value="id"> Id </option>
			<option value="retailer"> Retailer </option>
			<option value="date"> Date </option>
			<option value="status"> Status </option>
			<option value="approved"> Approval </option>
			</select>
			</div>
			
			<div class="input-box"> <input type="text" name="txtId" id="txtId" style="display:none;" /> </div>
			<div class="input-box">
			<select name="cmbRetailer" id="cmbRetailer" style="display:none;">
				<option value="" disabled selected>-- Select Retailer --</option>
				<?php while($rowSelectRetailer = mysqli_fetch_array($resultSelectRetailer)) { ?>
				<option value="<?php echo $rowSelectRetailer['retailer_id']; ?>"><?php echo $rowSelectRetailer['retailer_name']." (".$rowSelectRetailer['area_name'].")"; ?></option>
				<?php } ?>
			</select>
			</div>
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
				<th> Retailer </th>
				<th> Date </th>
				<th> Assigned Manufacturer </th>
				<th> Approved Status </th>
				<th> Order Status </th>
				<th> Details </th>
				<th> Actions </th>
			</tr>
			<?php $i=1; while($row_selectOrder = mysqli_fetch_array($result_selectOrder)) { ?>
			<tr>
				<td> <?php echo $row_selectOrder['order_id']; ?> </td>
				<td> <?php echo $row_selectOrder['retailer_name'] . ' (' . $row_selectOrder['area_code'] . ')'; ?> </td>
				<td> <?php echo date("d-m-Y",strtotime($row_selectOrder['date'])); ?> </td>
				<td>
					<?php 
					if(!empty($row_selectOrder['manufacturer_name'])) {
						echo '<span style="color: #28a745; font-weight: bold;">✅ ' . $row_selectOrder['manufacturer_name'] . '</span>';
					} else {
						echo '<span style="color: #dc3545; font-weight: bold;">⏳ Unassigned</span>';
					}
					?>
				</td>
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
						else {
							echo "Completed";
						}
					?>
				</td>
				<td> <a href="view_order_items.php?id=<?php echo $row_selectOrder['order_id']; ?>" style="color: #007bff; text-decoration: none; font-weight: bold;">📋 Details</a> </td>
				<td>
					<?php if(empty($row_selectOrder['manufacturer_name']) && $row_selectOrder['status'] == 0): ?>
						<!-- Unassigned Order - Show Assignment Form AND Cancel Button -->
						<form method="POST" style="display: inline-block;">
							<input type="hidden" name="order_id" value="<?php echo $row_selectOrder['order_id']; ?>">
							<select name="manufacturer_id" style="padding: 4px; margin-right: 5px; border: 1px solid #ccc; border-radius: 4px; font-size: 11px;" required>
								<option value="">Select Manufacturer</option>
								<?php 
								// Get manufacturers for dropdown
								$query_manufacturers = "SELECT man_id, man_name FROM manufacturer ORDER BY man_name";
								$result_manufacturers = mysqli_query($con, $query_manufacturers);
								while($manufacturer = mysqli_fetch_array($result_manufacturers)) { 
								?>
									<option value="<?php echo $manufacturer['man_id']; ?>">
										<?php echo $manufacturer['man_name']; ?>
									</option>
								<?php } ?>
							</select>
							<button type="submit" name="assign_order" 
									style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 11px; font-weight: bold;">
								🎯 Assign
							</button>
						</form>
						<button type="button" onclick="cancelOrder(<?php echo $row_selectOrder['order_id']; ?>, 'admin')" 
								class="cancel-btn" 
								style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; border: none; padding: 6px 12px; border-radius: 15px; cursor: pointer; font-size: 12px; font-weight: bold; transition: all 0.3s ease; margin-left: 5px;">
							🗑️ Cancel
						</button>
					<?php elseif($row_selectOrder['status'] == 2 && $row_selectOrder['cancelled_by'] == 'manufacturer'): ?>
						<!-- Cancelled by Manufacturer - Show Reassignment Form -->
						<form method="POST" style="display: inline-block;">
							<input type="hidden" name="order_id" value="<?php echo $row_selectOrder['order_id']; ?>">
							<select name="manufacturer_id" style="padding: 4px; margin-right: 5px; border: 1px solid #ccc; border-radius: 4px; font-size: 11px;" required>
								<option value="">Select Manufacturer</option>
								<?php 
								// Get manufacturers for dropdown
								$query_manufacturers2 = "SELECT man_id, man_name FROM manufacturer ORDER BY man_name";
								$result_manufacturers2 = mysqli_query($con, $query_manufacturers2);
								while($manufacturer2 = mysqli_fetch_array($result_manufacturers2)) { 
								?>
									<option value="<?php echo $manufacturer2['man_id']; ?>">
										<?php echo $manufacturer2['man_name']; ?>
									</option>
								<?php } ?>
							</select>
						<button type="submit" name="assign_order" 
								style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 11px; font-weight: bold;">
							🎯 Assign
						</button>
					</form>
					<button type="button" onclick="cancelOrder(<?php echo $row_selectOrder['order_id']; ?>, 'admin')" 
							class="cancel-btn" 
							style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; border: none; padding: 6px 12px; border-radius: 15px; cursor: pointer; font-size: 12px; font-weight: bold; transition: all 0.3s ease; margin-left: 5px;">
						🗑️ Cancel
					</button>
					<?php elseif($row_selectOrder['status'] == 2): ?>
						<!-- Cancelled by Retailer or Admin - No Reassignment -->
						<span style="color: <?php echo ($row_selectOrder['cancelled_by'] == 'admin') ? '#dc3545' : '#6c757d'; ?>; font-style: italic; font-size: 11px; font-weight: <?php echo ($row_selectOrder['cancelled_by'] == 'admin') ? 'bold' : 'normal'; ?>;">
							<?php if($row_selectOrder['cancelled_by'] == 'admin'): ?>
								🔒 Permanently Cancelled
							<?php else: ?>
								Cancelled by <?php echo ucfirst($row_selectOrder['cancelled_by']); ?>
							<?php endif; ?>
						</span>
					<?php elseif($row_selectOrder['status'] == 0): ?>
						<!-- Pending Order - Show Cancel Button -->
						<button type="button" onclick="cancelOrder(<?php echo $row_selectOrder['order_id']; ?>, 'admin')" 
								class="cancel-btn" 
								style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; border: none; padding: 6px 12px; border-radius: 15px; cursor: pointer; font-size: 12px; font-weight: bold; transition: all 0.3s ease;">
							🗑️ Cancel
						</button>
					<?php else: ?>
						<span style="color: #28a745; font-style: italic;">Completed</span>
					<?php endif; ?>
				</td>
			</tr>
			<?php $i++; } ?>
		</table>
		</form>
	</section>
	<?php
		include("../includes/footer.inc.php");
	?>
	
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
	
	<script type="text/javascript">
		var currentOrderId = null;
		var currentUserType = null;
		
		function cancelOrder(orderId, userType) {
			console.log('cancelOrder called with:', orderId, userType);
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
			console.log('currentOrderId:', currentOrderId);
			console.log('currentUserType:', currentUserType);
			
			if (!currentOrderId || !currentUserType) {
				alert('Error: Missing order information');
				return;
			}
			
			var reason = document.getElementById('cancelReason').value;
			console.log('Reason:', reason);
			
			// Show loading
			var modal = document.getElementById('cancelModal');
			modal.innerHTML = '<div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: #333;"><h3>Cancelling Order...</h3><p>Please wait...</p></div>';
			
			console.log('Sending AJAX request to ../cancel_order.php');
			
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
						location.reload(); // Refresh the page to show updated status
					} else {
						alert('❌ ' + response.message);
						closeCancelModal();
					}
				},
				error: function(xhr, status, error) {
					console.log('AJAX Error:', xhr.responseText);
					console.log('Status:', status);
					console.log('Error:', error);
					alert('❌ Error occurred while cancelling order. Please try again. Check console for details.');
					closeCancelModal();
				}
			});
		}
		
		$('#cmbFilter').change(function() {
			var selected = $(this).val();
			if(selected == "id"){
				$('#txtId').show();
				$('#cmbRetailer').hide();
				$('#datepicker').hide();
				$('#cmbStatus').hide();
				$('#cmbApproved').hide();
			}
			else if (selected == "retailer"){
				$('#txtId').hide();
				$('#cmbRetailer').show();
				$('#datepicker').hide();
				$('#cmbStatus').hide();
				$('#cmbApproved').hide();
			}
			else if (selected == "date"){
				$('#txtId').hide();
				$('#cmbRetailer').hide();
				$('#datepicker').show();
				$('#cmbStatus').hide();
				$('#cmbApproved').hide();
			}
			else if (selected == "status"){
				$('#txtId').hide();
				$('#cmbRetailer').hide();
				$('#datepicker').hide();
				$('#cmbStatus').show();
				$('#cmbApproved').hide();
			}
			else if (selected == "approved"){
				$('#txtId').hide();
				$('#cmbRetailer').hide();
				$('#datepicker').hide();
				$('#cmbStatus').hide();
				$('#cmbApproved').show();
			}
		});
		
		// Add hover effects for cancel buttons
		$(document).ready(function() {
			$('.cancel-btn').hover(
				function() {
					$(this).css('transform', 'translateY(-2px)');
					$(this).css('box-shadow', '0 4px 15px rgba(220, 53, 69, 0.3)');
				},
				function() {
					$(this).css('transform', 'translateY(0)');
					$(this).css('box-shadow', 'none');
				}
			);
		});
	</script>
</body>
</html>