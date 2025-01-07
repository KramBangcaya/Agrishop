<?php
// Include necessary configuration and database connection
require_once('admin/inc/config.php');

// Set response headers
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Initialize response array
$response = [
    'status' => 'error',
    'message' => 'Invalid request'
];

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $input = json_decode(file_get_contents("php://input"), true);

    // Validate required fields
    if (!empty($input['user_id']) && !empty($input['name']) && !empty($input['contact_number']) && !empty($input['address'])) {
        $userId = $input['user_id'];
        $name = $input['name'];
        $lastname = $input['lastname'] ?? null;
        $contactNumber = $input['contact_number'];
        $address = $input['address'];
        $password = !empty($input['cust_password']) ? password_hash($input['cust_password'], PASSWORD_BCRYPT) : null;

        try {
            // Use the existing PDO connection from config.php
            global $pdo;

            // Build the SQL query
            $query = "UPDATE users SET name = :name, lastname = :lastname, contact_number = :contact_number, address = :address";
            if ($password) {
                $query .= ", password = :password";
            }
            $query .= " WHERE id = :user_id";

            // Prepare and bind parameters
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->bindParam(':contact_number', $contactNumber);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':user_id', $userId);
            if ($password) {
                $stmt->bindParam(':password', $password);
            }

            // Execute the query
            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Profile updated successfully';
            } else {
                $response['message'] = 'Failed to update profile';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Missing required fields';
    }
}

// Output JSON response
echo json_encode($response);
?>
