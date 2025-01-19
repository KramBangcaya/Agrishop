<?php
require_once('admin/inc/config.php'); // Include your database configuration

// Check if the order ID and status are provided
if (isset($_POST['order_id']) && isset($_POST['status']) && isset($_POST['reason'])) {
    $orderId = $_POST['order_id'];
    $status = $_POST['status'];
    $reason = $_POST['reason'];
    $cancel = 'buyer';

    try {
        // Check the current status of the order
        $checkQuery = "SELECT order_status FROM Orders WHERE id = ?";
        $checkStmt = $pdo->prepare($checkQuery);
        $checkStmt->execute([$orderId]);
        $currentOrder = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($currentOrder) {
            $currentStatus = $currentOrder['order_status'];

            // Proceed only if the current status is "Pending"
            if ($currentStatus === 'Pending') {
                // Prepare the update query
                $query = "UPDATE Orders SET order_status = ?, reason_cancel = ?, cancel_by = ? WHERE id = ?";
                $stmt = $pdo->prepare($query);

                // Execute the update query
                if ($stmt->execute([$status, $reason, $cancel, $orderId])) {
                    // Return success response
                    echo json_encode(['success' => true]);
                } else {
                    // Return failure response
                    echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
                }
            } elseif ($currentStatus === 'For Delivery') {
                // Return response indicating the update is not allowed
                echo json_encode(['success' => false, 'message' => 'Order status "For Delivery" cannot be canceled. Need to reload']);
            } else {
                // Handle other statuses if needed
                echo json_encode(['success' => false, 'message' => 'Order cannot be canceled at its current status.']);
            }
        } else {
            // Return response if the order is not found
            echo json_encode(['success' => false, 'message' => 'Order not found']);
        }
    } catch (Exception $e) {
        // Handle exceptions
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    // Return error response if the required parameters are missing
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
