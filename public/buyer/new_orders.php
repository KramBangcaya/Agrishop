<?php
// Include the config.php file where the database connection is established
include("admin/inc/config.php");

// Allow access from any origin
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Set the header to return JSON
header('Content-Type: application/json');

// Check if seller_id is passed in the URL
if (isset($_GET['seller_id']) && is_numeric($_GET['seller_id'])) {
    $seller_id = $_GET['seller_id']; // Get the seller_id from the query string
} else {
    echo json_encode(["status" => "error", "message" => "Seller ID is required and must be a number."]);
    exit();
}

try {
    // Prepare the SQL query to count the total number of orders with order_status 'Pending'
    $sql = "
    SELECT COUNT(*) AS new_orders
    FROM orders
    WHERE order_status = 'Pending' AND seller_id = :seller_id;
    ";

    // Prepare the statement using the PDO instance from config.php
    $stmt = $pdo->prepare($sql);

    // Bind the seller_id parameter
    $stmt->bindParam(':seller_id', $seller_id, PDO::PARAM_INT);

    // Execute the statement
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if result is fetched
    if ($result) {
        // Output the result as JSON with status "success"
        echo json_encode(["status" => "success", "data" => $result]);
    } else {
        echo json_encode(["status" => "success", "data" => [], "message" => "No orders found with 'Pending' status."]);
    }

} catch (PDOException $e) {
    // Handle any connection or query errors
    echo json_encode(["status" => "error", "data" => [], "error" => "Error: " . $e->getMessage()]);
}

// Close the PDO connection (optional, as it's already handled in config.php)
$pdo = null;
?>
