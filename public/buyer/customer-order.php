<?php
require_once('header.php');
require_once('api-config.php');
?>

<?php
// Check if the customer is logged in or not
if(!isset($_SESSION['customer'])) {
    header('location: '.BASE_URL.'logout.php');
    exit;
} else {
    require_once('admin/inc/config.php'); // Include your database configuration
// var_dump($_SESSION['user_id']);
    // Get the email from the session
    $cust_email = $_SESSION['user_id'];
    // $buyer_id = $_SESSION[];
    // Fetch orders from the database
    $query = "SELECT * FROM Orders WHERE buyer_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$cust_email]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // var_dump($orders);
}

?>

<div class="page">
    <div class="container">


        <div class="row">
            <div class="col-md-12">
                <div class="user-content">

                <h1><button class="btn" onclick="window.history.back()"><i class="fa fa-arrow-left" aria-hidden="true"></i></button> <?php echo LANG_VALUE_25; ?></h1>
                    <h3 class="special"> </h3>
                    <div class="table-responsive">


                    <div class="row">
    <div class="col-md-4">
        <div class="row" style="margin-left: 10px;margin-bottom: 15px;"> <!-- Centering the inner row -->
            <div class="col-md-12 form-group">





                        </div>








<?php

// Fetch orders from the database
$queryCount = "SELECT COUNT(*) AS total_delivered FROM Orders WHERE buyer_id = ? AND order_status=?";
$stmtCount = $pdo->prepare($queryCount);
$stmtCount->execute([$_SESSION['user_id'], "Pending"]);
$countResult = $stmtCount->fetch(PDO::FETCH_ASSOC);

//$totalDelivered = $countResult['total_delivered'];
//echo "Total Pending Orders: " . $totalDelivered;


?>






<div class="order-section">
    <!-- Pending Orders -->
    <h2>Pending Orders</h2>
    <details>
    <summary style="cursor: pointer; font-size: 1.2em; font-weight: bold;color:rgb(54, 71, 228);">
        View Pending Orders (<?php echo $totalDelivered = $countResult['total_delivered']; ?>)
    </summary>

    <div style="margin-top: 10px;">


    <?php

// Query for the data rows
$queryData = "SELECT * FROM Orders WHERE buyer_id = ? AND order_status=? ORDER BY `orders`.`timedate` DESC";
$stmtData = $pdo->prepare($queryData);
$stmtData->execute([$_SESSION['user_id'], "Pending"]);
$orders = $stmtData->fetchAll(PDO::FETCH_ASSOC);




// Group orders by status
$groupedOrders = [
    'Pending' => [],
    'For Delivery' => [],
    'Delivered' => [],
    'Cancelled Order' => []
];

foreach ($orders as $order) {
    $groupedOrders[$order['order_status']][] = $order;
}
?>

  <?php foreach ($orders as $order): ?>

                    <!-- Check if the user has already provided feedback for this product -->
                    <?php
                $feedbackQuery = "SELECT * FROM Feedback WHERE order_id = ?";
                $stmt = $pdo->prepare($feedbackQuery);
                $stmt->execute([ $order['id']]);
                $feedback = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>


                    <h3> <span>Product Name: <?php echo htmlspecialchars($order['product_name']); ?></span></h3>


                    <div style="margin-top: 10px; font-size: medium;">
                    <label>Quantity: </label>
                    <span><?php echo htmlspecialchars($order['product_quantity'] ?? ''); ?>  <label>Total:</span> ₱<?php echo htmlspecialchars($order['totalPayment'] ?? ''); ?></label><br>

                    <label>Payment date time: </label>
                    <span><?php echo htmlspecialchars($order['timedate'] ?? ''); ?></span><br>

                    <label>Transaction ID: </label>
                    <span><?php echo htmlspecialchars($order['id'] ?? ''); ?></span><br>

                    <label>Seller Name: </label>
                    <span><?php echo htmlspecialchars($order['seller_name'] ?? ''); ?></span><br>

                    <label>Seller Number: </label>
                    <span><?php echo htmlspecialchars($order['seller_number'] ?? ''); ?></span><br>

                    <label>Seller Address: </label>
                    <span><?php echo htmlspecialchars($order['seller_address'] ?? ''); ?></span><br>

                    <label>Expected Delivery: </label>
                    <span>1-2 days</span><br>



                        <label>Order Status: </label>
                        <?php if ($order['order_status'] === "Delivered"): ?>
                            <span class="badge bg-danger w-100" style="background-color:green;">Delivered</span>
                        <?php elseif ($order['order_status'] === "For Delivery"): ?>
                            <span class="badge bg-danger w-100" style="background-color:gray;">For Delivery</span>
                        <?php elseif ($order['order_status'] === "Cancelled Order"): ?>
                                <span class="badge bg-danger w-100" style="background-color:red;">Cancelled Order</span>
                        <?php else: ?>
                            <span class="badge bg-danger w-100" style="background-color:red;">Pending</span>
                        <?php endif; ?>
                        <br>


                        <?php if ($order['order_status'] === 'For Delivery'): ?>
                                <h4>
                                    <a href="#" onclick="markAsDelivered(<?php echo $order['id']; ?>);" class="trash">
                                        Mark as Delivered <i class="fa fa-truck" style="color:green;"></i>
                                    </a>
                                </h4>
                            <?php elseif ($order['order_status'] === 'Delivered'): ?>
                                <h4 style="color: gray;">
                                    Order Delivered <i class="fa fa-check" style="color: green;"></i>
                                </h4>
                            <?php elseif ($order['order_status'] === 'Cancelled Order'): ?>
                                <h4 style="color: gray;">
                                    Order Cancelled <i class="fa fa-check" style="color: green;"></i>
                                </h4>
                            <?php else: ?>
                                <h4>
                                <a href="#" onclick="showCancelReasonForm(<?php echo $order['id']; ?>);" class="trash">
                                    Cancel Order <i class="fa fa-ban" style="color:red;"></i>
                                </a>
                                </h4>
                            <?php endif; ?>






                            <?php
                            if (!empty($order['reason_cancel'])) {
                                echo "<br><label>Cancellation Reason: </label>";
                                echo "<span>" . nl2br(htmlspecialchars($order['reason_cancel'])) . "</span>";
                            }
                            ?>

                            <div id="cancel-form-<?php echo $order['id']; ?>" style="display: none; margin-top: 10px;">

                                    <button onclick="cancelOrderWithReason(<?php echo $order['id']; ?>)">Enter a Reason</button>
                                    <button onclick="cancelCancellationForm(<?php echo $order['id']; ?>)">Cancel</button>
                                </div>

                        <?php if ($feedback): ?>
                        <h4>Your Feedback:</h4>
                        <div style="border: 2px solid #ddd; padding: 10px; border-radius: 5px; background-color: #f9f9f9;">
                        <p><strong>Rating:</strong> <?php echo htmlspecialchars($feedback['rating']); ?>/5</p>
                        <p><strong>Comment:</strong> <?php echo nl2br(htmlspecialchars($feedback['feedback'])); ?></p>
                        </div>
                    <?php else: ?>
                        <h4 onclick="toggleFeedbackForm(event)" style="cursor: pointer;">
                            Feedback <i class="fa fa-comments" style="color:green;"></i>
                        </h4>
                        <div id="feedback-form-<?php echo $order['id']; ?>" style="display: none; margin-top: 10px;">
                                                    <textarea id="feedback-text-<?php echo $order['id']; ?>" placeholder="Enter your feedback here..." rows="4" cols="50"></textarea>
                                                    <br>
                                                    <label for="rating">Rate your experience (1-5): </label>
                                                    <select id="rating-<?php echo $order['id']; ?>">
                                                        <option value="1">1 - Very Poor</option>
                                                        <option value="2">2 - Poor</option>
                                                        <option value="3">3 - Average</option>
                                                        <option value="4">4 - Good</option>
                                                        <option value="5">5 - Excellent</option>
                                                    </select>
                                                    <br><br>
                                                    <button onclick="submitFeedback(
                                                    <?php echo $order['id']; ?>,
                                                    <?php echo $_SESSION['user_id']; ?>,
                                                    '<?php echo addslashes($order['buyer_name']); ?>',
                                                    <?php echo $order['product_id']; ?>,
                                                    '<?php echo addslashes($order['product_name']); ?>'
                                                )">Submit</button>

                                                <button onclick="cancelFeedback(<?php echo $order['id']; ?>)">Cancel</button>
                                                                                                </div>
                                                                    </div>
                                <?php endif; ?><hr style="
    border: none;
    border-top: 2px solid #ccc;
    margin: 20px 0;
    width: 100%;
">
                            <?php endforeach; ?>

                            </div>


</details>

<h3 class="special"> </h3>
<!-- Success Modal -->
<div id="successModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('successModal')">&times;</span>
        <h2 id="successMessage"></h2>
        <button onclick="closeModal('successModal')">OK</button>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('errorModal')">&times;</span>
        <h2 id="errorMessage"></h2>
        <button onclick="closeModal('errorModal')">OK</button>
    </div>
</div>

<!-- Modal styles -->
<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0,0,0);
        background-color: rgba(0,0,0,0.4);
        padding-top: 60px;
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>


<!-- Toast Notification -->
<div id="toast" class="toast"></div>

<style>
    .toast {
        visibility: hidden;
        min-width: 250px;
        margin-left: -125px;
        background-color: #333;
        color: #fff;
        text-align: center;
        border-radius: 5px;
        padding: 15px;
        position: fixed;
        z-index: 1;
        left: 50%;
        bottom: 30px;
        font-size: 17px;
        opacity: 0;
        transition: opacity 0.5s, bottom 0.5s;
    }

    .toast.show {
        visibility: visible;
        opacity: 1;
        bottom: 50px;
    }
</style>





    </div>




    <?php

// Fetch orders from the database
$queryCount = "SELECT COUNT(*) AS total_delivereds FROM Orders WHERE buyer_id = ? AND order_status=?";
$stmtCount = $pdo->prepare($queryCount);
$stmtCount->execute([$_SESSION['user_id'], "For Delivery"]);
$countResult = $stmtCount->fetch(PDO::FETCH_ASSOC);

//$totalDelivereds = $countResult['total_delivereds'];
//echo "Total Pending Orders: " . $totalDelivered;


?>



    <!-- For Delivery Orders -->
    <h2>For Delivery Orders<?php
    // Query to check if this product is canceled by the seller
    $queryCancelBy = "SELECT COUNT(*) AS total_delivered1ser FROM Orders WHERE buyer_id = ? AND order_status = ?";
    $stmtCancelBy = $pdo->prepare($queryCancelBy);

    // Execute the query with the parameters
    $stmtCancelBy->execute([$_SESSION['user_id'], "For Delivery"]);

    // Fetch the result
    $cancelByResult = $stmtCancelBy->fetch(PDO::FETCH_ASSOC);

    // Display the red circle notification only if there are cancelled orders by the seller
    if ($cancelByResult && $cancelByResult['total_delivered1ser'] > 0) {
        $totalDelivered1s = $cancelByResult['total_delivered1ser'];
        echo '
        <span style="
            background: red;
            color: white;
            border-radius: 50%;
            padding: 5px;
            margin-left: 5px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 34px;
            height: 34px;
            font-size: 16px;
            text-align: center;
        ">
            ' . htmlspecialchars($totalDelivered1s) . '
        </span>';
    }
    ?></h2>

    <details>
    <summary style="cursor: pointer; font-size: 1.2em; font-weight: bold;color:rgb(54, 71, 228);">
        View For Delivery Orders (<?php echo $totalDelivereds = $countResult['total_delivereds']; ?>)
    </summary>

    <div style="margin-top: 10px;">

<?php
// Fetch orders from the database
$query = "SELECT * FROM Orders WHERE buyer_id = ? AND order_status=? ORDER BY `orders`.`timedate` DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$_SESSION['user_id'],"For Delivery"]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group orders by status
$groupedOrders = [
'Pending' => [],
'For Delivery' => [],
'Delivered' => [],
'Cancelled Order' => []
];

foreach ($orders as $order) {
$groupedOrders[$order['order_status']][] = $order;
}
?>

<?php foreach ($orders as $order): ?>

                <!-- Check if the user has already provided feedback for this product -->
                <?php
                $feedbackQuery = "SELECT * FROM Feedback WHERE order_id = ?";
                $stmt = $pdo->prepare($feedbackQuery);
                $stmt->execute([ $order['id']]);
                $feedback = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>


                <h3> <span>Product Name: <?php echo htmlspecialchars($order['product_name']); ?></span></h3>


                <div style="margin-top: 10px; font-size: medium;">
                <label>Quantity: </label>
                <span><?php echo htmlspecialchars($order['product_quantity'] ?? ''); ?>  <label>Total:</span> ₱<?php echo htmlspecialchars($order['totalPayment'] ?? ''); ?></label><br>

                <label>Payment date time: </label>
                <span><?php echo htmlspecialchars($order['timedate'] ?? ''); ?></span><br>

                <label>Transaction ID: </label>
                <span><?php echo htmlspecialchars($order['id'] ?? ''); ?></span><br>

                <label>Seller Name: </label>
                <span><?php echo htmlspecialchars($order['seller_name'] ?? ''); ?></span><br>

                <label>Seller Number: </label>
                <span><?php echo htmlspecialchars($order['seller_number'] ?? ''); ?></span><br>

                <label>Seller Address: </label>
                <span><?php echo htmlspecialchars($order['seller_address'] ?? ''); ?></span><br>

                <label>Expected Delivery: </label>
                <span>1-2 days</span><br>



                    <label>Order Status: </label>
                    <?php if ($order['order_status'] === "Delivered"): ?>
                        <span class="badge bg-danger w-100" style="background-color:green;">Delivered</span>
                    <?php elseif ($order['order_status'] === "For Delivery"): ?>
                        <span class="badge bg-danger w-100" style="background-color:gray;">For Delivery</span>
                    <?php elseif ($order['order_status'] === "Cancelled Order"): ?>
                            <span class="badge bg-danger w-100" style="background-color:red;">Cancelled Order</span>
                    <?php else: ?>
                        <span class="badge bg-danger w-100" style="background-color:red;">Pending</span>
                    <?php endif; ?>
                    <br>


                    <?php if ($order['order_status'] === 'For Delivery'): ?>
                            <h4>
                                <a href="#" onclick="markAsDelivered(<?php echo $order['id']; ?>);" class="trash">
                                    Mark as Delivered <i class="fa fa-truck" style="color:green;"></i>
                                </a>
                            </h4>
                        <?php elseif ($order['order_status'] === 'Delivered'): ?>
                            <h4 style="color: gray;">
                                Order Delivered <i class="fa fa-check" style="color: green;"></i>
                            </h4>
                        <?php elseif ($order['order_status'] === 'Cancelled Order'): ?>
                            <h4 style="color: gray;">
                                Order Cancelled <i class="fa fa-check" style="color: green;"></i>
                            </h4>
                        <?php else: ?>
                            <h4>
                            <a href="#" onclick="showCancelReasonForm(<?php echo $order['id']; ?>);" class="trash">
                                Cancel Order <i class="fa fa-ban" style="color:red;"></i>
                            </a>
                            </h4>
                        <?php endif; ?>






                        <?php
                        if (!empty($order['reason_cancel'])) {
                            echo "<br><label>Cancellation Reason: </label>";
                            echo "<span>" . nl2br(htmlspecialchars($order['reason_cancel'])) . "</span>";
                        }
                        ?>

                        <div id="cancel-form-<?php echo $order['id']; ?>" style="display: none; margin-top: 10px;">

                                <button onclick="cancelOrderWithReason(<?php echo $order['id']; ?>)">Enter a Reason</button>
                                <button onclick="cancelCancellationForm(<?php echo $order['id']; ?>)">Cancel</button>
                            </div>

                    <?php if ($feedback): ?>
                    <h4>Your Feedback:</h4>
                    <div style="border: 2px solid #ddd; padding: 10px; border-radius: 5px; background-color: #f9f9f9;">
                    <p><strong>Rating:</strong> <?php echo htmlspecialchars($feedback['rating']); ?>/5</p>
                    <p><strong>Comment:</strong> <?php echo nl2br(htmlspecialchars($feedback['feedback'])); ?></p>
                    </div>
                <?php else: ?>
                    <h4 onclick="toggleFeedbackForm(event)" style="cursor: pointer;">
                        Feedback <i class="fa fa-comments" style="color:green;"></i>
                    </h4>
                    <div id="feedback-form-<?php echo $order['id']; ?>" style="display: none; margin-top: 10px;">
                                                <textarea id="feedback-text-<?php echo $order['id']; ?>" placeholder="Enter your feedback here..." rows="4" cols="50"></textarea>
                                                <br>
                                                <label for="rating">Rate your experience (1-5): </label>
                                                <select id="rating-<?php echo $order['id']; ?>">
                                                    <option value="1">1 - Very Poor</option>
                                                    <option value="2">2 - Poor</option>
                                                    <option value="3">3 - Average</option>
                                                    <option value="4">4 - Good</option>
                                                    <option value="5">5 - Excellent</option>
                                                </select>
                                                <br><br>
                                                <button onclick="submitFeedback(
                                                <?php echo $order['id']; ?>,
                                                <?php echo $_SESSION['user_id']; ?>,
                                                '<?php echo addslashes($order['buyer_name']); ?>',
                                                <?php echo $order['product_id']; ?>,
                                                '<?php echo addslashes($order['product_name']); ?>'
                                            )">Submit</button>

                                            <button onclick="cancelFeedback(<?php echo $order['id']; ?>)">Cancel</button>
                                                                                            </div>
                                                                </div>


                            <?php endif; ?>
                            <hr style="
    border: none;
    border-top: 2px solid #ccc;
    margin: 20px 0;
    width: 100%;
">
                        <?php endforeach; ?>

                </div>


</details>

<h3 class="special"> </h3>







<?php

// Fetch orders from the database
$queryCount = "SELECT COUNT(*) AS total_deliveredss FROM Orders WHERE buyer_id = ? AND order_status=?";
$stmtCount = $pdo->prepare($queryCount);
$stmtCount->execute([$_SESSION['user_id'], "Delivered"]);
$countResult = $stmtCount->fetch(PDO::FETCH_ASSOC);

//$totalDelivereds = $countResult['total_delivereds'];
//echo "Total Pending Orders: " . $totalDelivered;


?>




    <!-- Delivered Orders -->
    <h2>Delivered Orders</h2>
    <details>
    <summary style="cursor: pointer; font-size: 1.2em; font-weight: bold;color:rgb(54, 71, 228);">
        View Delivered Orders (<?php echo $totalDeliveredss = $countResult['total_deliveredss']; ?>)
    </summary>
    <div style="margin-top: 10px;">


<?php
// Fetch orders from the database
$query = "SELECT * FROM Orders WHERE buyer_id = ? AND order_status=? ORDER BY `orders`.`timedate` DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$_SESSION['user_id'],"Delivered"]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group orders by status
$groupedOrders = [
'Pending' => [],
'For Delivery' => [],
'Delivered' => [],
'Cancelled Order' => []
];

foreach ($orders as $order) {
$groupedOrders[$order['order_status']][] = $order;
}
?>

<?php foreach ($orders as $order): ?>

                <!-- Check if the user has already provided feedback for this product -->
                <?php
                $feedbackQuery = "SELECT * FROM Feedback WHERE order_id = ?";
                $stmt = $pdo->prepare($feedbackQuery);
                $stmt->execute([ $order['id']]);
                $feedback = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>


                <h3> <span>Product Name: <?php echo htmlspecialchars($order['product_name']); ?></span></h3>


                <div style="margin-top: 10px; font-size: medium;">
                <label>Quantity: </label>
                <span><?php echo htmlspecialchars($order['product_quantity'] ?? ''); ?>  <label>Total:</span> ₱<?php echo htmlspecialchars($order['totalPayment'] ?? ''); ?></label><br>

                <label>Payment date time: </label>
                <span><?php echo htmlspecialchars($order['timedate'] ?? ''); ?></span><br>

                <label>Transaction ID: </label>
                <span><?php echo htmlspecialchars($order['id'] ?? ''); ?></span><br>

                <label>Seller Name: </label>
                <span><?php echo htmlspecialchars($order['seller_name'] ?? ''); ?></span><br>

                <label>Seller Number: </label>
                <span><?php echo htmlspecialchars($order['seller_number'] ?? ''); ?></span><br>

                <label>Seller Address: </label>
                <span><?php echo htmlspecialchars($order['seller_address'] ?? ''); ?></span><br>

                <label>Expected Delivery: </label>
                <span>1-2 days</span><br>



                    <label>Order Status: </label>
                    <?php if ($order['order_status'] === "Delivered"): ?>
                        <span class="badge bg-danger w-100" style="background-color:green;">Delivered</span>
                    <?php elseif ($order['order_status'] === "For Delivery"): ?>
                        <span class="badge bg-danger w-100" style="background-color:gray;">For Delivery</span>
                    <?php elseif ($order['order_status'] === "Cancelled Order"): ?>
                            <span class="badge bg-danger w-100" style="background-color:red;">Cancelled Order</span>
                    <?php else: ?>
                        <span class="badge bg-danger w-100" style="background-color:red;">Pending</span>
                    <?php endif; ?>
                    <br>


                    <?php if ($order['order_status'] === 'For Delivery'): ?>
                            <h4>
                                <a href="#" onclick="markAsDelivered(<?php echo $order['id']; ?>);" class="trash">
                                    Mark as Delivered <i class="fa fa-truck" style="color:green;"></i>
                                </a>
                            </h4>
                        <?php elseif ($order['order_status'] === 'Delivered'): ?>
                            <h4 style="color: gray;">
                                Order Delivered <i class="fa fa-check" style="color: green;"></i>
                            </h4>
                        <?php elseif ($order['order_status'] === 'Cancelled Order'): ?>
                            <h4 style="color: gray;">
                                Order Cancelled <i class="fa fa-check" style="color: green;"></i>
                            </h4>
                        <?php else: ?>
                            <h4>
                            <a href="#" onclick="showCancelReasonForm(<?php echo $order['id']; ?>);" class="trash">
                                Cancel Order <i class="fa fa-ban" style="color:red;"></i>
                            </a>
                            </h4>
                        <?php endif; ?>






                        <?php
                        if (!empty($order['reason_cancel'])) {
                            echo "<br><label>Cancellation Reason: </label>";
                            echo "<span>" . nl2br(htmlspecialchars($order['reason_cancel'])) . "</span>";
                        }
                        ?>

                        <div id="cancel-form-<?php echo $order['id']; ?>" style="display: none; margin-top: 10px;">

                                <button onclick="cancelOrderWithReason(<?php echo $order['id']; ?>)">Enter a Reason</button>
                                <button onclick="cancelCancellationForm(<?php echo $order['id']; ?>)">Cancel</button>
                            </div>

                    <?php if ($feedback): ?>
                    <h4>Your Feedback:</h4>
                    <div style="border: 2px solid #ddd; padding: 10px; border-radius: 5px; background-color: #f9f9f9;">
                    <p><strong>Rating:</strong> <?php echo htmlspecialchars($feedback['rating']); ?>/5</p>
                    <p><strong>Comment:</strong> <?php echo nl2br(htmlspecialchars($feedback['feedback'])); ?></p>
                    </div>
                <?php else: ?>
                    <h4 onclick="toggleFeedbackForm(event)" style="cursor: pointer;">
                        Feedback <i class="fa fa-comments" style="color:green;"></i>
                    </h4>
                    <div id="feedback-form-<?php echo $order['id']; ?>" style="display: none; margin-top: 10px;">
                                                <textarea id="feedback-text-<?php echo $order['id']; ?>" placeholder="Enter your feedback here..." rows="4" cols="50"></textarea>
                                                <br>
                                                <label for="rating">Rate your experience (1-5): </label>
                                                <select id="rating-<?php echo $order['id']; ?>">
                                                    <option value="1">1 - Very Poor</option>
                                                    <option value="2">2 - Poor</option>
                                                    <option value="3">3 - Average</option>
                                                    <option value="4">4 - Good</option>
                                                    <option value="5">5 - Excellent</option>
                                                </select>
                                                <br><br>
                                                <button onclick="submitFeedback(
                                                <?php echo $order['id']; ?>,
                                                <?php echo $_SESSION['user_id']; ?>,
                                                '<?php echo addslashes($order['buyer_name']); ?>',
                                                <?php echo $order['product_id']; ?>,
                                                '<?php echo addslashes($order['product_name']); ?>'
                                            )">Submit</button>

                                            <button onclick="cancelFeedback(<?php echo $order['id']; ?>)">Cancel</button>
                                                                                            </div>
                                                                </div>


                            <?php endif; ?>
                            <hr style="
    border: none;
    border-top: 2px solid #ccc;
    margin: 20px 0;
    width: 100%;
">
                        <?php endforeach; ?>
                        </details>

                        <h3 class="special"> </h3>







<?php

// Fetch orders from the database
$queryCount = "SELECT COUNT(*) AS total_delivered1s FROM Orders WHERE buyer_id = ? AND order_status=?";
$stmtCount = $pdo->prepare($queryCount);
$stmtCount->execute([$_SESSION['user_id'], "Cancelled Order"]);
$countResult = $stmtCount->fetch(PDO::FETCH_ASSOC);

//$totalDelivered = $countResult['total_delivered'];
//echo "Total Pending Orders: " . $totalDelivered;


?>



<!-- Cancelled Orders -->
<h2>
    Cancelled Orders<?php
    // Query to check if this product is canceled by the seller
    $queryCancelBy = "SELECT COUNT(*) AS total_delivered1ser FROM Orders WHERE buyer_id = ? AND order_status = ? AND cancel_by = ?";
    $stmtCancelBy = $pdo->prepare($queryCancelBy);

    // Execute the query with the parameters
    $stmtCancelBy->execute([$_SESSION['user_id'], "Cancelled Order", "seller"]);

    // Fetch the result
    $cancelByResult = $stmtCancelBy->fetch(PDO::FETCH_ASSOC);

    // Display the red circle notification only if there are cancelled orders by the seller
    if ($cancelByResult && $cancelByResult['total_delivered1ser'] > 0) {
        $totalDelivered1s = $cancelByResult['total_delivered1ser'];
        echo '
        <span style="
            background: red;
            color: white;
            border-radius: 50%;
            padding: 5px;
            margin-left: 5px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 34px;
            height: 34px;
            font-size: 16px;
            text-align: center;
        ">
            ' . htmlspecialchars($totalDelivered1s) . '
        </span>';
    }
    ?>
</h2>





<details>
    <summary style="cursor: pointer; font-size: 1.2em; font-weight: bold;color:rgb(54, 71, 228);">
        View Cancelled Orders (<?php echo $totalDelivered1s = $countResult['total_delivered1s']; ?>)
    </summary>

    <div style="margin-top: 10px;">

        <?php
        // Fetch orders from the database
        $query = "SELECT * FROM Orders WHERE buyer_id = ? AND order_status=? ORDER BY `orders`.`timedate` DESC";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$_SESSION['user_id'], "Cancelled Order"]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($orders as $order): ?>

<h3>
    <span>Product Name: <?php echo htmlspecialchars($order['product_name']); ?></span>
    <?php
    // Query to check if this product is canceled by the seller
    $queryCancelBy = "SELECT id FROM Orders WHERE buyer_id = ? AND order_status = ? AND cancel_by = ? AND id = ?";
    $stmtCancelBy = $pdo->prepare($queryCancelBy);
    $stmtCancelBy->execute([$_SESSION['user_id'], "Cancelled Order", "seller", $order['id']]);

    // Fetch the result
    $cancelByResult = $stmtCancelBy->fetch(PDO::FETCH_ASSOC);

    // Display the red circle notification only if `cancel_by = 'seller'`
    if ($cancelByResult) {
        echo '
        <span style="
            background: red;
            color: white;
            border-radius: 50%;
            padding: 5px;
            margin-left: 5px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 34px;
            height: 34px;
            font-size: 16px;
            text-align: center;
        ">
            1
        </span>';
    }
    ?>
</h3>

            <div style="margin-top: 10px; font-size: medium;">
                <label>Quantity: </label>
                <span><?php echo htmlspecialchars($order['product_quantity'] ?? ''); ?>
                    <label>Total:</span> ₱<?php echo htmlspecialchars($order['totalPayment'] ?? ''); ?></label><br>

                <label>Payment date time: </label>
                <span><?php echo htmlspecialchars($order['timedate'] ?? ''); ?></span><br>

                <label>Transaction ID: </label>
                <span><?php echo htmlspecialchars($order['id'] ?? ''); ?></span><br>

                <label>Seller Name: </label>
                <span><?php echo htmlspecialchars($order['seller_name'] ?? ''); ?></span><br>

                <label>Seller Number: </label>
                <span><?php echo htmlspecialchars($order['seller_number'] ?? ''); ?></span><br>

                <label>Seller Address: </label>
                <span><?php echo htmlspecialchars($order['seller_address'] ?? ''); ?></span><br>

                <label>Expected Delivery: </label>
                <span>1-2 days</span><br>

                <label>Order Status: </label>
                <span class="badge bg-danger w-100" style="background-color:red;">Cancelled Order</span><br>

                <?php if (!empty($order['reason_cancel'])): ?>
                    <br><label>Cancellation Reason: </label>
                    <span><?php echo nl2br(htmlspecialchars($order['reason_cancel'])); ?></span><br>
                <?php endif; ?>

                <!-- Feedback Section -->
                <?php
                $feedbackQuery = "SELECT * FROM Feedback WHERE order_id = ?";
                $stmt = $pdo->prepare($feedbackQuery);
                $stmt->execute([ $order['id']]);
                $feedback = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>

                <?php if ($feedback): ?>
                    <h4>Your Feedback:</h4>
                    <div style="border: 2px solid #ddd; padding: 10px; border-radius: 5px; background-color: #f9f9f9;">
                        <p><strong>Rating:</strong> <?php echo htmlspecialchars($feedback['rating']); ?>/5</p>
                        <p><strong>Comment:</strong> <?php echo nl2br(htmlspecialchars($feedback['feedback'])); ?></p>
                    </div>
                <?php else: ?>
                    <h4 onclick="toggleFeedbackForm(event)" style="cursor: pointer;">
                        Feedback <i class="fa fa-comments" style="color:green;"></i>
                    </h4>





                    <div id="feedback-form-<?php echo $order['id']; ?>" style="display: none; margin-top: 10px;">
                        <textarea id="feedback-text-<?php echo $order['id']; ?>" placeholder="Enter your feedback here..." rows="4" cols="50"></textarea>
                        <br>
                        <label for="rating">Rate your experience (1-5): </label>
                        <select id="rating-<?php echo $order['id']; ?>">
                            <option value="1">1 - Very Poor</option>
                            <option value="2">2 - Poor</option>
                            <option value="3">3 - Average</option>
                            <option value="4">4 - Good</option>
                            <option value="5">5 - Excellent</option>
                        </select>
                        <br><br>
                        <button onclick="submitFeedback(
                            <?php echo $order['id']; ?>,
                            <?php echo $_SESSION['user_id']; ?>,
                            '<?php echo addslashes($order['buyer_name']); ?>',
                            <?php echo $order['product_id']; ?>,
                            '<?php echo addslashes($order['product_name']); ?>'
                        )">Submit</button>
                        <button onclick="cancelFeedback(<?php echo $order['id']; ?>)">Cancel</button>
                    </div>
                <?php endif; ?>

                <?php
// Query to check if there are any orders canceled by the seller
$queryCancelBy = "SELECT id, cancel_by FROM Orders WHERE id = ? AND order_status = ?";
$stmtCancelBy = $pdo->prepare($queryCancelBy);
$stmtCancelBy->execute([$order['id'], "Cancelled Order"]);
$cancelByResult = $stmtCancelBy->fetchAll(PDO::FETCH_ASSOC);

// Display the "Mark as Read" button only if `cancel_by = 'seller'`
if (!empty($cancelByResult)) {
    foreach ($cancelByResult as $order) {
        if ($order['cancel_by'] === 'seller') {
            echo '
            <h4 onclick="markAsRead(' . $order['id'] . ')" style="cursor: pointer; color: red;">
                Mark as Read <i class="fa fa-commenting" aria-hidden="true"></i>
            </h4>';
        }
    }
}
?>
<hr style="
    border: none;
    border-top: 2px solid #ccc;
    margin: 20px 0;
    width: 100%;
">
<script>
function markAsRead(orderId) {
    // Send AJAX request to update cancel_by
    fetch('update_cancel_by.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ orderId: orderId }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success notification and reload the page
            showToast('Marked as read successfully!');
            location.reload(); // Reload the page to reflect the update
        } else {
            showToast('Failed to mark as read.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred.');
    });
}
</script>
            </div>

        <?php endforeach; ?>
    </div>
</details>




</div>




</div>




<!-- Mark As Delivered Modal -->
<div id="markAsDeliveredModal" class="modal">
    <div class="modal-content">
        <h3>Mark Order as Delivered</h3>
        <p>Are you sure you want to mark this order as delivered?</p>
        <div class="modal-actions">
            <button class="btn btn-success" onclick="confirmMarkAsDelivered()">Confirm</button>
            <button class="btn btn-secondary" onclick="closeMarkAsDeliveredModal()">Close</button>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="toast"></div>





<div id="cancelOrderModal" class="modal">
    <div class="modal-content">
        <h3>Cancel Order</h3>
        <p>Are you sure you want to cancel this order?</p>
        <textarea id="cancel-reason-input" placeholder="Enter cancellation reason" rows="3" style="width: 100%;"></textarea>
        <div class="modal-actions">
            <button class="btn btn-success" onclick="confirmCancelOrder()">Confirm</button>
            <button class="btn btn-secondary" onclick="closeCancelOrderModal()">Close</button>
        </div>
    </div>
</div>



                    </div>
                </div>






            </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>

function showToast(message) {
    const toast = document.getElementById('toast');
    toast.innerText = message;
    toast.className = 'toast show';
    setTimeout(() => {
        toast.className = 'toast';
    }, 3000); // Toast disappears after 3 seconds
}


                            function showCancelReasonForm(orderId) {
                                // Show the cancellation reason form
                                const cancelForm = document.getElementById('cancel-form-' + orderId);
                                if (cancelForm) {
                                    cancelForm.style.display = 'block';
                                }
                            }
                            function cancelCancellationForm(orderId) {
                                // Hide the cancellation reason form without submitting
                                const cancelForm = document.getElementById('cancel-form-' + orderId);
                                if (cancelForm) {
                                    cancelForm.style.display = 'none';
                                }
                            }
                            function toggleFeedbackForm(event) {
                                event.preventDefault();
                                const feedbackForm = event.target.nextElementSibling;
                                if (feedbackForm) {
                                    feedbackForm.style.display = feedbackForm.style.display === 'none' ? 'block' : 'none';
                                }
                            }

                            function submitFeedback(orderId, buyerId, buyerName, productId, productName) {
                                const feedbackTextarea = document.getElementById('feedback-text-' + orderId);
                                const ratingSelect = document.getElementById('rating-' + orderId);

                                if (feedbackTextarea && ratingSelect) {
                                    const feedback = feedbackTextarea.value.trim();
                                    const rating = ratingSelect.value;

                                    if (feedback && rating) {
                                        fetch('submit-feedback.php', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/x-www-form-urlencoded',
                                            },
                                            body: new URLSearchParams({
                                                buyer_id: buyerId,
                                                buyer_name: buyerName,
                                                product_id: productId,
                                                product_name: productName,
                                                feedback: feedback,
                                                rating: rating,
                                                order_id: orderId
                                            }),
                                        })
                                            .then((response) => response.json())
                                            .then((data) => {
                                                if (data.success) {
                                                    showToast('Feedback submitted successfully!');  // alert('Feedback submitted successfully!');
                                                    document.getElementById('feedback-form-' + orderId).style.display = 'none';
                                                    location.reload();
                                                } else {
                                                    showToast('Error: ' + data.message);a//alert('Error: ' + data.message);
                                                }
                                            })
                                            .catch((error) => {
                                                console.error('Error:', error);
                                                showToast('Failed to submit feedback.');// alert('Failed to submit feedback.');
                                            });
                                    } else {
                                        showToast('Please complete all fields before submitting.');// alert('Please complete all fields before submitting.');
                                    }
                                }
                            }

                            function cancelFeedback(orderId) {
                                const feedbackForm = document.getElementById('feedback-form-' + orderId);
                                if (feedbackForm) {
                                    feedbackForm.style.display = 'none';
                                }
                            }

                        function cancelOrderWithReason(orderId) {
                            openCancelOrderModal(orderId);
                        }


                        let currentOrderId = null; // Store the order ID globally
let currentReasonCancel = null; // Store the cancellation reason globally

function openCancelOrderModal(orderId) {
    currentOrderId = orderId; // Set the current order ID
    document.getElementById('cancelOrderModal').style.display = 'flex'; // Show the modal
}

function closeCancelOrderModal() {
    document.getElementById('cancelOrderModal').style.display = 'none'; // Hide the modal
}

function confirmCancelOrder() {
    const reasonCancel = document.getElementById('cancel-reason-input').value.trim();

    if (!reasonCancel) {
        showToast('Please provide a reason for the cancellation.');
        return;
    }

    // Proceed with the cancellation
    fetch('cancel-order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            order_id: currentOrderId,
            status: 'Cancelled Order',
            reason: reasonCancel
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showToast('Order has been cancelled with reason: ' + reasonCancel);
                location.reload(); // Reload the page to reflect changes
            } else {
                showToast('Error: ' + data.message);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            showToast('Failed to cancel order.');
            location.reload();
        });

    closeCancelOrderModal(); // Close the modal after the action
}

function showToast(message) {
    const toast = document.getElementById('toast');
    toast.innerText = message;
    toast.className = 'toast show';
    setTimeout(() => {
        toast.className = 'toast';
    }, 13000); // Toast disappears after 3 seconds
}

// Store the current order ID globally  let currentOrderId = null;

function openMarkAsDeliveredModal(orderId) {
    currentOrderId = orderId; // Set the current order ID
    document.getElementById('markAsDeliveredModal').style.display = 'flex'; // Show the modal
}

function closeMarkAsDeliveredModal() {
    document.getElementById('markAsDeliveredModal').style.display = 'none'; // Hide the modal
}

function confirmMarkAsDelivered() {
    fetch('update-order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            order_id: currentOrderId,
            status: 'Delivered',

        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showToast('Order marked as delivered.');
                location.reload();
                // Hide the "Mark as Delivered" button
                const markAsDeliveredLink = document.querySelector(
                    `a[onclick="openMarkAsDeliveredModal(${currentOrderId});"]`
                );
                if (markAsDeliveredLink) {
                    markAsDeliveredLink.style.display = 'none'; // Hides the button
                }

                // Update the displayed order status text
                const orderStatusElement = document.querySelector(`#order-status-${currentOrderId}`);
                if (orderStatusElement) {
                    orderStatusElement.innerHTML =
                        'Delivered <i class="fa fa-check" style="color: green;"></i>';
                    orderStatusElement.style.color = 'gray'; // Change the text color
                }


            } else {
                showToast('Error: ' + data.message);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            showToast('Failed to update order status.');
        });

    closeMarkAsDeliveredModal(); // Close the modal after confirmation
}

function showToast(message) {
    const toast = document.getElementById('toast');
    toast.innerText = message;
    toast.className = 'toast show';
    setTimeout(() => {
        toast.className = 'toast';
    }, 3000); // Toast disappears after 3 seconds
}

                        function markAsDelivered(orderId) {
                          openMarkAsDeliveredModal(orderId); // Open the modal when the button/link is clicked

                        }
                        </script>

                <div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php
                // Ensure the user_id is set in the session
                if (isset($_SESSION['user_id'])) {
                    $user_id = $_SESSION['user_id'];
                } else {
                    $user_id = null; // Handle this appropriately if user_id is not set
                }

                // Include the file without query string

                ?>
            </div>




        </div>
    </div>
</div>

<?php
if (isset($_SESSION['customer'])) {
    ?>
    <div class="col-md-12">
        <div class="user-content" style="
            position: fixed; /* Keep it fixed in the viewport */
            bottom: 0; /* Position at the bottom */
            left: 0; /* Align to the left */
            width: 100%; /* Full width */
            z-index: 1000; /* Ensure it stays above other content */
            background-color: #fff; /* White background for contrast */
            padding: 10px; /* Add spacing inside */
            box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.1); /* Add shadow above the bar */
        ">
            <div style="text-align: center; margin-right: 10px; margin-bottom: 10px;">
                <a href="index.php" class="btn btn-primary"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
                <?php
                $statement = $pdo->prepare("SELECT * FROM tbl_page WHERE id=1");
                $statement->execute();
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                foreach ($result as $row) {
                    $about_title = $row['about_title'];
                    $faq_title = $row['faq_title'];
                    $blog_title = $row['blog_title'];
                    $contact_title = $row['contact_title'];
                    $pgallery_title = $row['pgallery_title'];
                    $vgallery_title = $row['vgallery_title'];
                }
                ?>
                <a href="customer-profile-update.php?id=<?php echo $user_id; ?>" class="btn btn-primary">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Profile
                </a>
                <a href="reportseller.php" class="btn btn-primary">
                    <i class="fa fa-flag" aria-hidden="true"></i> Report
                </a>
                <a href="customer-order.php?id=<?php echo $user_id; ?>" class="btn btn-primary">
                    <i class="fa fa-shopping-basket" aria-hidden="true"></i> Orders
                </a>
            </div>
        </div>
    </div>
    <?php
}
?>
