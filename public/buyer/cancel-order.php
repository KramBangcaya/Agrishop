<?php
require_once('admin/inc/config.php'); // Include your database configuration

// Check if the order ID and status are provided
if (isset($_POST['order_id']) && isset($_POST['status']) && isset($_POST['reason'])) {
    $orderId = $_POST['order_id'];
    $status = $_POST['status'];
    $reason = $_POST['reason'];
    $cancel = $_POST['cancel_by'];

    $cancel = 'buyer';

    // Prepare the update query
    $query = "UPDATE Orders SET order_status = ?, reason_cancel = ?, cancel_by = ? WHERE id = ?";
    $stmt = $pdo->prepare($query);

    // Execute the query
<<<<<<< HEAD
    if ($stmt->execute([$status, $reason, $cancel , $orderId])) {
=======
    if ($stmt->execute([$status, $reason, $orderId, $cancel])) {
>>>>>>> 8f56a836fc48221a2c371f0bed5c8088dde83710
        // Return success response
        echo json_encode(['success' => true]);
    } else {
        // Return failure response
        echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
    }
} else {
    // Return error response if the required parameters are missing
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
