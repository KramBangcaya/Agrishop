<?php
// Include the database configuration file
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

try {
    // Check if seller_id is provided in the request
    if (!isset($_GET['seller_id']) || empty($_GET['seller_id'])) {
        throw new Exception('Missing required parameter: seller_id');
    }

    // Sanitize and fetch the seller_id from GET parameters
    $seller_id = intval($_GET['seller_id']);

    // SQL query to fetch rows filtered by seller_id
    $query = "
    SELECT
        o.*,
        f.feedback,
        f.rating
    FROM
        orders o
    LEFT JOIN
        feedback f
    ON
        o.id = f.order_id
    WHERE
        o.seller_id = :seller_id
    AND
        o.order_status = 'Delivered'
";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':seller_id', $seller_id, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch all results as an associative array
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the data as JSON
    echo json_encode([
        'status' => 'success',
        'data' => $orders
    ]);
} catch (Exception $e) {
    // Return an error response if something fails
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
