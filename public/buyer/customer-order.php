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
                    <h1><?php echo LANG_VALUE_25; ?></h1>
                    <h3 class="special"> </h3>
                    <div class="table-responsive">


                    <div class="row">
    <div class="col-md-4">
        <div class="row" style="margin: 0 auto;"> <!-- Centering the inner row -->
            <div class="col-md-12 form-group">
                <?php foreach ($orders as $order): ?>

                    <!-- Check if the user has already provided feedback for this product -->
                    <?php
                    $feedbackQuery = "SELECT * FROM Feedback WHERE buyer_id = ? AND product_id = ?";
                    $stmt = $pdo->prepare($feedbackQuery);
                    $stmt->execute([$_SESSION['user_id'], $order['product_id']]);
                    $feedback = $stmt->fetch(PDO::FETCH_ASSOC);
                    ?>

                    <h2> <span><?php echo htmlspecialchars($order['product_name']); ?></span></h2>


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
                                    <textarea id="cancel-reason-<?php echo $order['id']; ?>" placeholder="Please provide a reason for cancelling..." rows="4" cols="50"></textarea><br>
                                    <button onclick="cancelOrderWithReason(<?php echo $order['id']; ?>)">Submit Cancellation</button>
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
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <script>
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
                                            }),
                                        })
                                            .then((response) => response.json())
                                            .then((data) => {
                                                if (data.success) {
                                                    alert('Feedback submitted successfully!');
                                                    document.getElementById('feedback-form-' + orderId).style.display = 'none';
                                                } else {
                                                    alert('Error: ' + data.message);
                                                }
                                            })
                                            .catch((error) => {
                                                console.error('Error:', error);
                                                alert('Failed to submit feedback.');
                                            });
                                    } else {
                                        alert('Please complete all fields before submitting.');
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
                            const reason_cancel = document.getElementById('cancel-reason-' + orderId).value.trim();

                            if (reason_cancel) {
                                if (confirm('Are you sure you want to cancel this order?')) {
                                    fetch('cancel-order.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded',
                                        },
                                        body: new URLSearchParams({
                                            order_id: orderId,
                                            status: 'Cancelled Order',
                                            reason: reason_cancel
                                        }),
                                    })
                                    .then((response) => response.json())
                                    .then((data) => {
                                        if (data.success) {
                                            alert('Order has been cancelled with reason: ' + reason_cancel);
                                            location.reload(); // Reload the page to reflect changes
                                        } else {
                                            alert('Error: ' + data.message);
                                        }
                                    })
                                    .catch((error) => {
                                        console.error('Error:', error);
                                        alert('Failed to cancel order.');
                                    });
                                }
                            } else {
                                alert('Please provide a reason for the cancellation.');
                            }
                        }
                        function markAsDelivered(orderId) {
                            if (confirm('Are you sure you want to mark this order as delivered?')) {
                                fetch('update-order.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                    },
                                    body: new URLSearchParams({
                                        order_id: orderId,
                                        status: 'Delivered'
                                    }),
                                })
                                .then((response) => response.json())
                                .then((data) => {
                                    if (data.success) {
                                        alert('Order marked as delivered.');

                                        // Hide the "Mark as Delivered" button
                                        const markAsDeliveredLink = document.querySelector(`a[onclick="markAsDelivered(${orderId});"]`);
                                        if (markAsDeliveredLink) {
                                            markAsDeliveredLink.style.display = 'none'; // Hides the button
                                        }

                                        // Optionally update the displayed order status text
                                        const orderStatusElement = document.querySelector(`#order-status-${orderId}`);
                                        if (orderStatusElement) {
                                            orderStatusElement.innerHTML = 'Delivered <i class="fa fa-check" style="color: green;"></i>';
                                            orderStatusElement.style.color = 'gray'; // Change the text color
                                        }
                                    } else {
                                        alert('Error: ' + data.message);
                                    }
                                })
                                .catch((error) => {
                                    console.error('Error:', error);
                                    alert('Failed to update order status.');
                                });
                            }
                        }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require_once('footer.php'); ?>
