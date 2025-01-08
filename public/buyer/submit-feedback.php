<?php
require_once('admin/inc/config.php'); // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect data from the request
    $buyerId = $_POST['buyer_id'];
    $buyerName = $_POST['buyer_name'];
    $productId = $_POST['product_id'];
    $productName = $_POST['product_name'];
    // $buyerName = 'N/A';
    $feedback = $_POST['feedback'];
    $rating = $_POST['rating'];
    $orderId = $_POST['orderId'];

    // Validate inputs
    // if (empty($buyerId) || empty($buyerName) || empty($productId) || empty($productName) || empty($feedback) || empty($rating)) {
    //     echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    //     exit;
    // }

    $dateSubmitted = date('Y-m-d H:i:s');

    // dd($dateSubmitted);
    // echo $dateSubmitted;

    // Insert feedback into the database
    $query = "INSERT INTO feedback (buyer_id, buyer_name, product_id, product_name, feedback, rating, date, order_id)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);

    //
    // $buyerName,  $productName,
    try {
        $stmt->execute([$buyerId, $buyerName, $productId, $productName, $feedback, $rating, $dateSubmitted, $orderId]);
        echo json_encode(['success' => true, 'message' => 'Feedback submitted successfully.']);
    } catch (Exception $e) {
        echo $e;
        echo json_encode(['success' => false, 'message' => 'Failed to submit feedback.']);
    }
}
?>
