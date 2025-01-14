<?php
// Include your database configuration
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

// Get the seller_id from the GET request (you can replace 51 with a dynamic value if needed)
$seller_id = isset($_GET['seller_id']) ? intval($_GET['seller_id']) : 51; // Default to 51 if not specified

// Prepare the SQL query
$sql = "SELECT seller_id, SUM(totalPayment) AS total_sales
        FROM orders
        WHERE seller_id = :seller_id
        AND order_status = 'delivered'
        GROUP BY seller_id";

try {
    // Prepare the statement using the PDO object
    $stmt = $pdo->prepare($sql);

    // Bind the seller_id parameter to the query
    $stmt->bindParam(':seller_id', $seller_id, PDO::PARAM_INT);

    // Execute the query
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // Return the data as JSON, wrapped in "data" key
        echo json_encode([
            'status' => 'success',
            'data' => [
                'seller_id' => $result['seller_id'],
                'total_sales' => $result['total_sales']
            ]
        ]);
    } else {
        // If no results found, return a message inside "data"
        echo json_encode([
            'status' => 'error',
            'data' => [
                'message' => 'No data found'
            ]
        ]);
    }
} catch (PDOException $e) {
    // Handle any potential errors and return them inside "data"
    echo json_encode([
        'status' => 'error',
        'data' => [
            'message' => 'Database error: ' . $e->getMessage()
        ]
    ]);
}
?>
