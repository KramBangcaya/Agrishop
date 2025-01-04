<?php
require_once('admin/inc/config.php');

// Check if the request is valid
if (isset($_POST['order_id']) && isset($_POST['status'])) {
    $orderId = $_POST['order_id'];
    $status = $_POST['status'];

    // Prepare the SQL statement to update the order status
    $query = "UPDATE Orders SET order_status = ? WHERE id = ?";
    $stmt = $pdo->prepare($query);

    // Execute the statement with the new status
    $stmt->execute([$status, $orderId]);

    // Check if the update was successful
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
