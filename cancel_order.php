<?php
require("includes/config.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Response function for AJAX
function sendResponse($success, $message, $redirect = null) {
    $response = array(
        'success' => $success,
        'message' => $message
    );
    if ($redirect) {
        $response['redirect'] = $redirect;
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Check if request is POST and has required parameters
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['order_id']) || !isset($_POST['user_type'])) {
    sendResponse(false, 'Invalid request parameters');
}

$order_id = intval($_POST['order_id']);
$user_type = $_POST['user_type'];
$reason = isset($_POST['reason']) ? mysqli_real_escape_string($con, $_POST['reason']) : '';

// Validate order exists and get order details
$query_checkOrder = "SELECT o.*, r.retailer_name, r.retailer_id 
                    FROM orders o 
                    JOIN retailer r ON o.retailer_id = r.retailer_id 
                    WHERE o.order_id = '$order_id'";
$result_checkOrder = mysqli_query($con, $query_checkOrder);

if (!$result_checkOrder || mysqli_num_rows($result_checkOrder) == 0) {
    sendResponse(false, 'Order not found');
}

$order = mysqli_fetch_array($result_checkOrder);

// Check if order is already cancelled by admin (permanent cancellation)
if ($order['status'] == 2 && $order['cancelled_by'] == 'admin') {
    sendResponse(false, 'Order is already permanently cancelled by admin');
}

// Check if order is completed
if ($order['status'] == 1) {
    sendResponse(false, 'Cannot cancel completed orders');
}

// Permission checks based on user type
switch ($user_type) {
    case 'admin':
        if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
            sendResponse(false, 'Unauthorized access - Admin not logged in');
        }
        // Admin can cancel any order
        break;
        
    case 'manufacturer':
        if (!isset($_SESSION['manufacturer_login']) || $_SESSION['manufacturer_login'] !== true) {
            sendResponse(false, 'Unauthorized access - Manufacturer not logged in');
        }
        
        $manufacturer_id = $_SESSION['manufacturer_id'];
        
        // Check if this order is assigned to this manufacturer
        if ($order['assigned_manufacturer_id'] != $manufacturer_id) {
            sendResponse(false, 'You can only cancel orders assigned to you');
        }
        break;
        
    case 'retailer':
        if (!isset($_SESSION['retailer_login']) || $_SESSION['retailer_login'] !== true) {
            sendResponse(false, 'Unauthorized access - Retailer not logged in');
        }
        
        $retailer_id = $_SESSION['retailer_id'];
        
        // Check if this order belongs to the retailer
        if ($order['retailer_id'] != $retailer_id) {
            sendResponse(false, 'You can only cancel your own orders');
        }
        
        // Retailers can only cancel non-approved orders
        if ($order['approved'] == 1) {
            sendResponse(false, 'Cannot cancel orders that have been approved');
        }
        break;
        
    default:
        sendResponse(false, 'Invalid user type');
}

// Begin transaction
mysqli_autocommit($con, false);

try {
    // If manufacturer cancels, unassign them so admin can reassign to another manufacturer
    $unassign_manufacturer = ($user_type == 'manufacturer') ? ", assigned_manufacturer_id = NULL" : "";
    
    // Update order status to cancelled (status = 2)
    $query_cancelOrder = "UPDATE orders 
                         SET status = 2, 
                             approved = 0,
                             cancellation_reason = '$reason',
                             cancelled_by = '$user_type',
                             cancelled_date = NOW()
                             $unassign_manufacturer
                         WHERE order_id = '$order_id'";
    
    if (!mysqli_query($con, $query_cancelOrder)) {
        throw new Exception('Failed to cancel order: ' . mysqli_error($con));
    }
    
    // If there are any invoices for this order, mark them as cancelled
    $query_cancelInvoices = "UPDATE invoice 
                            SET status = 'Cancelled' 
                            WHERE order_id = '$order_id'";
    mysqli_query($con, $query_cancelInvoices);
    
    // Log the cancellation for audit trail
    $current_user = '';
    switch ($user_type) {
        case 'admin':
            $current_user = 'Admin';
            break;
        case 'manufacturer':
            $current_user = 'Manufacturer ID: ' . $_SESSION['manufacturer_id'];
            break;
        case 'retailer':
            $current_user = 'Retailer ID: ' . $_SESSION['retailer_id'];
            break;
    }
    
    $log_message = "Order #$order_id cancelled by $current_user. Reason: $reason";
    error_log($log_message);
    
    // Commit transaction
    mysqli_commit($con);
    
    sendResponse(true, 'Order cancelled successfully');
    
} catch (Exception $e) {
    // Rollback transaction
    mysqli_rollback($con);
    sendResponse(false, 'Error cancelling order: ' . $e->getMessage());
}
?>
