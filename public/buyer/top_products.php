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
    echo json_encode(["error" => "Seller ID is required and must be a number."]);
    exit();
}

try {
    // Prepare the SQL query
    $sql = "
    WITH RankedProducts AS (
        SELECT
            product_name,
            seller_name,
            product_id,
            SUM(product_quantity) AS total_quantity,
            ROW_NUMBER() OVER (PARTITION BY seller_name ORDER BY SUM(product_quantity) DESC) AS rank
        FROM orders
        WHERE seller_id = :seller_id AND order_status = 'delivered'
        GROUP BY product_name, seller_name, product_id
    )
    SELECT
        product_name,
        seller_name,
        total_quantity,
        rank
    FROM RankedProducts
    WHERE rank <= 5
    ORDER BY rank;
    ";

    // Prepare the statement using the PDO instance from config.php
    $stmt = $pdo->prepare($sql);

    // Bind the seller_id parameter
    $stmt->bindParam(':seller_id', $seller_id, PDO::PARAM_INT);

    // Execute the statement
    $stmt->execute();

    // Fetch all results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if there are results
    if ($results) {
        // Output the results with a "data" key as a JSON response, including the rank
        echo json_encode(["data" => $results]);
    } else {
        echo json_encode(["data" => [], "message" => "No products found."]);
    }

} catch (PDOException $e) {
    // Handle any connection or query errors
    echo json_encode(["data" => [], "error" => "Error: " . $e->getMessage()]);
}

// Close the PDO connection (optional, as it's already handled in config.php)
$pdo = null;
?>
