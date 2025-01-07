<?php
ob_start();
require_once('api-config.php');
require_once('header.php');

// Ensure that the user is logged in
if (!isset($_SESSION['customer'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Loop through the product details
    foreach ($_POST['product_name'] as $key => $product_name) {
        $buyer_id = $_SESSION['customer']['user_id'];
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

        // Handle file upload
        if (isset($_FILES['photo']['name'][$key]) && $_FILES['photo']['error'][$key] == 0) {
            $upload_dir = 'uploads/';
            $photo_name = basename($_FILES['photo']['name'][$key]);
            $photo = $upload_dir . $photo_name;

            if (!move_uploaded_file($_FILES['photo']['tmp_name'][$key], $photo)) {
                $photo = null; // Proceed without photo if upload fails
            }
        }

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
        $buyerName = $_SESSION['customer']['name'] . ' ' . $_SESSION['customer']['lastname'];
        $sellerName = $seller_name . ' ' . $seller_last;

        $stmt->bindParam(':buyer_name', $buyerName);
        $stmt->bindParam(':buyer_id', $buyer_id);
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
            if (!$stmt->execute()) {
                echo "Error placing order for $product_name.<br>";
                print_r($stmt->errorInfo());
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    }

    // Clear output buffer and redirect
    if (ob_get_level()) {
        ob_end_clean();
    }
    unset($_SESSION['cart_p_id']);
    unset($_SESSION['cart_p_qty']);
    unset($_SESSION['cart_p_name']);
    unset($_SESSION['cart_s_name']);
    unset($_SESSION['cart_p_current_price']);
    unset($_SESSION['cart_s_last']);
    header('Location: customer-order.php');
    exit;
}

require_once('footer.php');
ob_end_flush();
