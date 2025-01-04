<?php
ob_start(); // Start output buffering

require_once('api-config.php');
require_once('header.php');

// Ensure that the user is logged in
if (!isset($_SESSION['customer'])) {
    header('Location: login.php');
    exit;
}

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST['product_name'] as $key => $product_name) {
        // $userids = $_POST['user_id'][$key];
        $buyer_id = $_POST['buyer_id'][$key];
        $product_quantity = $_POST['product_quantity'][$key];
        $product_price = $_POST['product_price'][$key];
        $product_id = $_POST['product_id'][$key];
        $seller_name = $_POST['seller_name'][$key];
        $seller_last = $_POST['seller_last'][$key];
        $payment_method = $_POST['payment_method'][$key];
        $seller_number = $_POST['seller_contact'][$key];
        $seller_address = $_POST['seller_address'][$key];
        $seller_id = $_POST['seller_id'][$key];
        $order_status = "Pending";

        $total_price = $product_quantity * $product_price;
        $photo = null;

        // Handle file upload if a photo is provided
        if (isset($_FILES['photo']['name'][$key]) && $_FILES['photo']['error'][$key] == 0) {
            $upload_dir = 'uploads/';
            $photo_name = basename($_FILES['photo']['name'][$key]);
            $photo = $upload_dir . $photo_name;

            if (!move_uploaded_file($_FILES['photo']['tmp_name'][$key], $photo)) {
                echo "Error uploading file for product: $product_name";
                continue;
            }
        }
        $query = "INSERT INTO orders (buyer_name, buyer_id, product_name, seller_name, seller_number, seller_address, order_status, photo, totalPayment, timedate, product_quantity, seller_id, buyer_address, product_id)
        VALUES (:buyer_name, :buyer_id, :product_name, :seller_name, :seller_number, :seller_address ,:order_status, :photo, :totalPayment, :timedate, :product_quantity, :seller_id, :buyer_address, :product_id)";
$stmt = $pdo->prepare($query);

        // Prepare SQL query for inserting the order
        $query = "
            INSERT INTO orders (
                buyer_name,
                buyer_id,
                product_name,
                seller_name,
                seller_number,
                seller_address,
                order_status,
                photo,
                totalPayment,
                timedate,
                product_quantity,
                seller_id,
                buyer_address,
                product_id
            )
            VALUES (
                :buyer_name,
                :buyer_id,
                :product_name,
                :seller_name,
                :seller_number,
                :seller_address,
                :order_status,
                :photo,
                :totalPayment,
                :timedate,
                :product_quantity,
                :seller_id,
                :buyer_address,
                :product_id
            )";

        $stmt = $pdo->prepare($query);
        $timedate = date('Y-m-d H:i:s');

// var_dump($_SESSION['customer']['name']);


// Debug variable values
var_dump($product_name, $seller_name, $seller_number, $photo, $total_price, $product_quantity, $seller_address, $seller_number, $seller_id, $product_id);


$buyerName = $_SESSION['customer']['name'] . ' ' . $_SESSION['customer']['lastname'];
$sellerName = $seller_name . ' ' . $seller_last;
$stmt->bindParam(':buyer_name', $buyerName);
$stmt->bindParam(':buyer_id', $_SESSION['customer']['user_id']);
$stmt->bindParam(':product_name', $product_name);
$stmt->bindParam(':seller_name', $sellerName);
$stmt->bindParam(':seller_number', $seller_number);
$stmt->bindParam(':seller_address', $seller_address);
$stmt->bindParam(':order_status', $order_status);
$stmt->bindParam(':photo', $photo);
$stmt->bindParam(':totalPayment', $total_price);
$stmt->bindParam(':timedate', $timedate);
$stmt->bindParam(':product_quantity', $product_quantity);
$stmt->bindParam(':seller_id', $seller_id);
$stmt->bindParam(':buyer_address', $_SESSION['customer']['address']);
$stmt->bindParam(':product_id', $product_id);

        try {
            if ($stmt->execute()) {
                // Sanitize the URL parameter
        // echo $userids;

                // Clean any output before redirecting
                if (ob_get_level()) {
                    ob_end_clean(); // Clean (erase) any buffered output
                }

                // Send the header for redirection
                //header("Location: $redirect_url");
                exit;
            } else {
                echo "Error placing order for $product_name.<br>";
                print_r($stmt->errorInfo()); // Log detailed error for debugging
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }

    }
}

require_once('footer.php');
ob_end_flush(); // End output buffering
?>
