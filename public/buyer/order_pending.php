<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$input = json_decode(file_get_contents("php://input"), true);

if (isset($input['order_id']) && isset($input['order_status']) && isset($input['reason_cancel'])) {
    $order_id = $input['order_id'];
    $order_status = $input['order_status'];
    $reason_cancel = $input['reason_cancel'];


    // Include the config file to get the PDO database connection
    require_once('admin/inc/config.php');  // Adjust the path if needed

    // Check if $pdo is available
    if (!isset($pdo)) {
        echo json_encode(['status' => 'error', 'message' => 'Database connection not established.']);
        exit();
    }

    // Prepare the SQL statement using PDO
    try {
        $stmt = $pdo->prepare("UPDATE Orders SET order_status = :order_status, reason_cancel = :reason_cancel WHERE id = :order_id");
        $stmt->bindParam(':order_status', $order_status, PDO::PARAM_STR);
        $stmt->bindParam(':reason_cancel', $reason_cancel, PDO::PARAM_STR);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Order status updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update order status.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error executing query: ' . $e->getMessage()]);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
}
?>
