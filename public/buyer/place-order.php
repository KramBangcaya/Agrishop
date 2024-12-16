<?php

// Include necessary files
require_once('api-config.php');
require_once('header.php');

// Ensure that the user is logged in
if (!isset($_SESSION['customer'])) {
    header('location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST['product_name'] as $key => $product_name) {
        $product_quantity = $_POST['product_quantity'][$key];
        $product_price = $_POST['product_price'][$key];
        $seller_name = $_POST['seller_name'][$key];
        $seller_last = $_POST['seller_last'][$key];
        $payment_method = $_POST['payment_method'][$key];
        $seller_number = $_POST['seller_contact'][$key];
        $seller_address = $_POST['seller_address'][$key];
        $seller_id = $_POST['seller_id'][$key];
        $order_status = "Pending";

        $total_price = $product_quantity * $product_price;

        $photo = null;
        if (isset($_FILES['photo']['name'][$key]) && $_FILES['photo']['error'][$key] == 0) {
            $upload_dir = 'uploads/';
            $photo = $upload_dir . basename($_FILES['photo']['name'][$key]);

            if (!move_uploaded_file($_FILES['photo']['tmp_name'][$key], $photo)) {
                echo "Error uploading file for product: $product_name";
                continue; // Skip this product if file upload fails
            }
        }
        $query = "INSERT INTO orders (buyer_name, product_name, seller_name, seller_number, seller_address, order_status, photo, totalPayment, timedate, product_quantity, seller_id)
        VALUES (:buyer_name, :product_name, :seller_name, :seller_number, :seller_address ,:order_status, :photo, :totalPayment, :timedate, :product_quantity, :seller_id)";
$stmt = $pdo->prepare($query);

$timedate = date('Y-m-d H:i:s');

// Debug session variables
if (!isset($_SESSION['customer']['name'])) {
  die("Session customer name is not set.");
}

// Debug variable values
var_dump($product_name, $seller_name, $seller_number, $photo, $total_price, $product_quantity, $seller_address, $seller_number, $seller_id);

$stmt->bindParam(':buyer_name', $_SESSION['customer']['name']);
$stmt->bindParam(':product_name', $product_name);
$stmt->bindParam(':seller_name', $seller_name);
$stmt->bindParam(':seller_number', $seller_number);
$stmt->bindParam(':seller_address', $seller_address);
$stmt->bindParam(':order_status', $order_status);
$stmt->bindParam(':photo', $photo);
$stmt->bindParam(':totalPayment', $total_price);
$stmt->bindParam(':timedate', $timedate);
$stmt->bindParam(':product_quantity', $product_quantity);
$stmt->bindParam(':seller_id', $seller_id);

try {
  if ($stmt->execute()) {
      echo "Order for $product_name placed successfully!<br>";
  } else {
      echo "Error placing order for $product_name.<br>";
      print_r($stmt->errorInfo()); // Display detailed error
  }
} catch (PDOException $e) {
  echo "Database error: " . $e->getMessage();
}

    }
}


?>



<?php require_once('footer.php'); ?>
