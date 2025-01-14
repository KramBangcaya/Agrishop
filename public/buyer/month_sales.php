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
    // Prepare the SQL query to get total payment per month
    $sql = "
    SELECT
        SUM(totalPayment) AS total_payment_per_month,
        MONTH(timedate) AS month
    FROM orders
    WHERE seller_id = :seller_id
    GROUP BY MONTH(timedate)
    ORDER BY month;
    ";

    // Prepare the statement using the PDO instance from config.php
    $stmt = $pdo->prepare($sql);

    // Bind the seller_id parameter
    $stmt->bindParam(':seller_id', $seller_id, PDO::PARAM_INT);

    // Execute the statement
    $stmt->execute();

    // Fetch all results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if results are found
    if ($results) {
        // Output the result as JSON with status "success"
        echo json_encode(["status" => "success", "data" => $results]);
    } else {
        echo json_encode(["status" => "success", "data" => [], "message" => "No data found for the given seller."]);
    }

} catch (PDOException $e) {
    // Handle any connection or query errors
    echo json_encode(["status" => "error", "data" => [], "error" => "Error: " . $e->getMessage()]);
}

// Close the PDO connection (optional, as it's already handled in config.php)
$pdo = null;
?>
