<?php
// Include database connection
include("admin/inc/config.php");

// Set headers to allow API access
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Check the HTTP method
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Method Not Allowed. Use POST instead."]);
    exit;
}

// Read the input data
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["message" => "Invalid input data."]);
    exit;
}

// Validate required fields
$requiredFields = ['user_id', 'name', 'lastname', 'address', 'email', 'contact_number'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(["message" => "Field '$field' is required."]);
        exit;
    }
}

// Assign variables
$user_id = $data['user_id'];
$name = $data['name'];
$lastname = $data['lastname'];
$address = $data['address'];
$email = $data['email'];
$contact_number = $data['contact_number'];

try {
    // Prepare the SQL statement
    $sql = "UPDATE users SET
                name = :name,
                lastname = :lastname,
                address = :address,
                email = :email,
                contact_number = :contact_number
            WHERE user_id = :user_id";

    $stmt = $pdo->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':lastname', $lastname);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':contact_number', $contact_number);
    $stmt->bindParam(':user_id', $user_id);

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(["message" => "User updated successfully."]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to update user."]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database error: " . $e->getMessage()]);
}
?>
