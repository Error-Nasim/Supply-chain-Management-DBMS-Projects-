<?php
	require("../includes/config.php");
	include("../includes/validate_data.php");
	error_reporting(0);
	session_start();
		if(isset($_SESSION['manufacturer_login'])) {
			if($_SESSION['manufacturer_login'] == true) {
				$error = "";
				$success = "";
				$manufacturer_id = $_SESSION['manufacturer_id'];
				
				// Handle toggle confirmation status
				if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['toggle_confirm'])) {
					$order_id = intval($_POST['order_id']);
					
					// Verify order belongs to this manufacturer
					$query_checkOrder = "SELECT order_id, approved FROM orders WHERE order_id='$order_id' AND assigned_manufacturer_id='$manufacturer_id'";
					$result_checkOrder = mysqli_query($con, $query_checkOrder);
					
					if(mysqli_num_rows($result_checkOrder) > 0) {
						$order = mysqli_fetch_array($result_checkOrder);
						$new_status = ($order['approved'] == 1) ? 0 : 1;
						
						// If confirming order (changing to approved=1), check stock availability
						if($new_status == 1) {
							$canConfirm = true;
							$insufficientProducts = array();
							
							// Get ordered items
							$queryOrderItems = "SELECT pro_id, quantity FROM order_items WHERE order_id='$order_id'";
							$resultOrderItems = mysqli_query($con, $queryOrderItems);
							
							while($orderItem = mysqli_fetch_array($resultOrderItems)) {
								$pro_id = $orderItem['pro_id'];
								$ordered_qty = $orderItem['quantity'];
								
								// Get manufacturer's current stock for this product
								$queryStock = "SELECT quantity FROM manufacturer_stock 
											   WHERE manufacturer_id='$manufacturer_id' AND product_id='$pro_id'";
								$resultStock = mysqli_query($con, $queryStock);
								
								if(mysqli_num_rows($resultStock) > 0) {
									$stockRow = mysqli_fetch_array($resultStock);
									$available_qty = $stockRow['quantity'];
									
									if($available_qty < $ordered_qty) {
										// Insufficient stock
										$canConfirm = false;
										
										// Get product name for error message
										$queryProd = "SELECT pro_name FROM products WHERE pro_id='$pro_id'";
										$resultProd = mysqli_query($con, $queryProd);
										$prodRow = mysqli_fetch_array($resultProd);
										
										$insufficientProducts[] = $prodRow['pro_name'] . " (Need: $ordered_qty, Available: $available_qty)";
									}
								} else {
									// No stock record for this product
									$canConfirm = false;
									
									// Get product name for error message
									$queryProd = "SELECT pro_name FROM products WHERE pro_id='$pro_id'";
									$resultProd = mysqli_query($con, $queryProd);
									$prodRow = mysqli_fetch_array($resultProd);
									
									$insufficientProducts[] = $prodRow['pro_name'] . " (Need: $ordered_qty, Available: 0)";
								}
							}
							
							if(!$canConfirm) {
								$errorMsg = "Not enough quantity to confirm this order:\\n\\n" . implode("\\n", $insufficientProducts);
								echo "<script>alert('$errorMsg'); window.location.href='view_orders.php';</script>";
								exit();
							}
							
							// If we reach here, stock is sufficient - update stock quantities
							mysqli_query($con, "START TRANSACTION");
							$stockUpdateSuccess = true;
							
							// Re-query order items to update stock
							$queryOrderItems2 = "SELECT pro_id, quantity FROM order_items WHERE order_id='$order_id'";
							$resultOrderItems2 = mysqli_query($con, $queryOrderItems2);
							
							while($orderItem2 = mysqli_fetch_array($resultOrderItems2)) {
								$pro_id = $orderItem2['pro_id'];
								$ordered_qty = $orderItem2['quantity'];
								
								$queryUpdateStock = "UPDATE manufacturer_stock 
													SET quantity = quantity - $ordered_qty 
													WHERE manufacturer_id='$manufacturer_id' AND product_id='$pro_id' AND quantity >= $ordered_qty";
								
								if(!mysqli_query($con, $queryUpdateStock) || mysqli_affected_rows($con) == 0) {
									$stockUpdateSuccess = false;
									break;
								}
							}
							
							if(!$stockUpdateSuccess) {
								mysqli_query($con, "ROLLBACK");
								echo "<script>alert('Failed to update stock. Order not confirmed.'); window.location.href='view_orders.php';</script>";
								exit();
							}
							
							mysqli_query($con, "COMMIT");
						}
						
						$query_updateApproved = "UPDATE orders SET approved='$new_status' WHERE order_id='$order_id'";
						if(mysqli_query($con, $query_updateApproved)) {
							$status_text = ($new_status == 1) ? "confirmed" : "unconfirmed";
							$success = "Order #$order_id has been $status_text successfully!";
						} else {
							$error = "Failed to update order status: " . mysqli_error($con);
						}
					} else {
						$error = "Unauthorized access to order";
					}
				}
				
				$querySelectRetailer = "SELECT *,area.area_id AS area_id FROM retailer,area WHERE retailer.area_id = area.area_id";
				$resultSelectRetailer = mysqli_query($con,$querySelectRetailer);
			if($_SERVER['REQUEST_METHOD'] == "POST" && !isset($_POST['toggle_confirm'])) {
				if(isset($_POST['cmbFilter'])) {
					if(!empty($_POST['txtId'])) {
						$result = validate_number($_POST['txtId']);
						if($result == 1) {
							$order_id = $_POST['txtId'];
							$manufacturer_id = $_SESSION['manufacturer_id'];
							
							// Only show order if it's assigned to this manufacturer
							$query_selectOrder = "SELECT o.*, r.retailer_name, r.retailer_phone, a.area_name 
							                     FROM orders o
							                     JOIN retailer r ON o.retailer_id = r.retailer_id 
							                     JOIN area a ON r.area_id = a.area_id
							                     WHERE o.order_id = '$order_id' AND o.assigned_manufacturer_id = '$manufacturer_id'";
							$result_selectOrder = mysqli_query($con,$query_selectOrder);
							$row_selectOrder = mysqli_fetch_array($result_selectOrder);
							if(empty($row_selectOrder)){
							   $error = "* No order was found with this ID or it's not assigned to you";
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
						$manufacturer_id = $_SESSION['manufacturer_id'];
						$query_selectOrder = "SELECT o.*, r.retailer_name, r.retailer_phone, a.area_name 
						                     FROM orders o
						                     JOIN retailer r ON o.retailer_id = r.retailer_id 
						                     JOIN area a ON r.area_id = a.area_id
					                     WHERE o.retailer_id = '$retailer_id' AND o.assigned_manufacturer_id = '$manufacturer_id'
					                     ORDER BY CASE WHEN o.status = 2 THEN 1 ELSE 0 END, o.order_id DESC";
						$result_selectOrder = mysqli_query($con,$query_selectOrder);
						$row_selectOrder = mysqli_fetch_array($result_selectOrder);
						if(empty($row_selectOrder)){
						   $error = "* No order was found of the selected Retailer";
						}
						else {
							mysqli_data_seek($result_selectOrder,0);
						}
					}
					else if(!empty($_POST['txtDate'])) {
						$date = $_POST['txtDate'];
						$manufacturer_id = $_SESSION['manufacturer_id'];
						$query_selectOrder = "SELECT o.*, r.retailer_name, r.retailer_phone, a.area_name 
						                     FROM orders o
						                     JOIN retailer r ON o.retailer_id = r.retailer_id 
						                     JOIN area a ON r.area_id = a.area_id
					                     WHERE o.date = '$date' AND o.assigned_manufacturer_id = '$manufacturer_id'
					                     ORDER BY CASE WHEN o.status = 2 THEN 1 ELSE 0 END, o.order_id DESC";
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
						$manufacturer_id = $_SESSION['manufacturer_id'];
						$query_selectOrder = "SELECT o.*, r.retailer_name, r.retailer_phone, a.area_name 
						                     FROM orders o
						                     JOIN retailer r ON o.retailer_id = r.retailer_id 
						                     JOIN area a ON r.area_id = a.area_id
					                     WHERE o.status = '$status' AND o.assigned_manufacturer_id = '$manufacturer_id'
					                     ORDER BY CASE WHEN o.status = 2 THEN 1 ELSE 0 END, o.order_id DESC";
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
						$manufacturer_id = $_SESSION['manufacturer_id'];
						$query_selectOrder = "SELECT o.*, r.retailer_name, r.retailer_phone, a.area_name 
						                     FROM orders o
						                     JOIN retailer r ON o.retailer_id = r.retailer_id 
						                     JOIN area a ON r.area_id = a.area_id
					                     WHERE o.approved = '$approved' AND o.assigned_manufacturer_id = '$manufacturer_id'
					                     ORDER BY CASE WHEN o.status = 2 THEN 1 ELSE 0 END, o.order_id DESC";
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
				// Get manufacturer ID from session
				$manufacturer_id = $_SESSION['manufacturer_id'];
				
				// Only show orders that have been assigned to this manufacturer by admin
				$query_selectOrder = "SELECT o.*, r.retailer_name, r.retailer_phone, a.area_name 
				                     FROM orders o
				                     JOIN retailer r ON o.retailer_id = r.retailer_id 
				                     JOIN area a ON r.area_id = a.area_id
			                     WHERE o.assigned_manufacturer_id = '$manufacturer_id'
			                     ORDER BY CASE WHEN o.status = 2 THEN 1 ELSE 0 END, o.order_id DESC";
				$result_selectOrder = mysqli_query($con,$query_selectOrder);
			}
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
		include("../includes/nav_manufacturer.inc.php");
		include("../includes/aside_manufacturer.inc.php");
	?>
	<section>
		<h1>Orders</h1>
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
				<option value="<?php echo $rowSelectRetailer['retailer_id']; ?>"><?php echo $rowSelectRetailer['area_code']." (".$rowSelectRetailer['area_name'].")"; ?></option>
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
		
		<?php if(!empty($success)): ?>
			<div style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 12px; border-radius: 5px; margin: 15px 0;">
				<strong>✓ Success:</strong> <?php echo $success; ?>
			</div>
		<?php endif; ?>
		
		<form action="" method="POST" class="form">
		<table class="table_displayData" style="margin-top:20px;">
			<tr>
				<th> Order ID </th>
				<th> Retailer </th>
				<th> Date </th>
				<th> Approved Status </th>
				<th> Order Status </th>
				<th> Details </th>
				<th> Confirm </th>
				<th> Generate Invoice </th>
				<th> Cancel </th>
			</tr>
			<?php $i=1; while($row_selectOrder = mysqli_fetch_array($result_selectOrder)) { ?>
			<tr>
			
				<td> <?php echo $row_selectOrder['order_id']; ?> </td>
				<td> <?php echo $row_selectOrder['retailer_name']; ?> </td>
				
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
						<form method="POST" style="display: inline-block; margin: 0;">
							<input type="hidden" name="order_id" value="<?php echo $row_selectOrder['order_id']; ?>">
							<?php if($row_selectOrder['approved'] == 0): ?>
								<button type="submit" name="toggle_confirm"
										style="background: #28a745; color: white; padding: 5px 10px; border: none; border-radius: 4px; font-size: 11px; cursor: pointer; display: inline-block;">
									✓ Confirm
								</button>
							<?php else: ?>
								<button type="submit" name="toggle_confirm"
										style="background: #ffc107; color: #000; padding: 5px 10px; border: none; border-radius: 4px; font-size: 11px; cursor: pointer; display: inline-block;">
									↻ Unconfirm
								</button>
							<?php endif; ?>
						</form>
					<?php elseif($row_selectOrder['status'] == 2): ?>
						<span style="color: #6c757d; font-style: italic; font-size: 11px;">Cancelled</span>
					<?php elseif($row_selectOrder['approved'] == 1): ?>
						<span style="color: #28a745; font-weight: bold; font-size: 11px;">✓ Confirmed</span>
					<?php endif; ?>
				</td>
				<td>
					<?php
						if($row_selectOrder['approved'] == 1 && $row_selectOrder['status'] == 0) {
						?>
							<a href="generate_invoice.php?id=<?php echo $row_selectOrder['order_id']; ?>">+ Invoice</a>
						<?php
						}
						?>
				</td>
				<td>
					<?php if($row_selectOrder['status'] == 0): ?>
						<button type="button" onclick="cancelOrder(<?php echo $row_selectOrder['order_id']; ?>, 'manufacturer')" 
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
			<p style="margin-bottom: 15px; text-align: center; font-size: 12px; color: #6c757d;">
				<strong>Note:</strong> If you cancel this order, the admin will be able to reassign it to another manufacturer.
			</p>
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