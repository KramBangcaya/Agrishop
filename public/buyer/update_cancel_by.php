
<?php
session_start();
require_once('admin/inc/config.php'); // Include your database configuration

// Decode the JSON request
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['orderId'])) {
    $orderId = $data['orderId'];

    // Update the `cancel_by` field to 'buyer'
    $queryUpdate = "UPDATE Orders SET cancel_by = 'buyer' WHERE id = ? AND buyer_id = ?";
    $stmtUpdate = $pdo->prepare($queryUpdate);
    $result = $stmtUpdate->execute([$orderId, $_SESSION['user_id']]);

    // Return a JSON response
    echo json_encode(['success' => $result]);
} else {
    echo json_encode(['success' => false]);
}
?>
