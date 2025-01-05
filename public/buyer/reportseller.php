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
    $query = "SELECT * FROM reports WHERE userID = ?";
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
                    <h1>Report History</h1><a style="float: right;" href="customer-billing-shipping-update.php"><button style="float: right;" class="btn btn-danger">Report Seller</button></a><br>
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

                    <h2>Seller Name:</h2>
                    <span><?php echo htmlspecialchars($order['buyer_name']); ?></span>

                    <div style="margin-top: 10px; font-size: medium;">


                        <label>Report date time: </label>
                        <span><?php echo htmlspecialchars($order['created_at']); ?></span><br>



                        <label>Reason: </label>
                        <span><?php echo htmlspecialchars($order['reason']); ?></span><br>

                        <label>Reply: </label>
                        <span><?php echo htmlspecialchars($order['reply']); ?></span><br>
                        <label>Reply Report date time: </label>
                        <span><?php echo htmlspecialchars($order['updated_at']); ?></span><br>

                        <label>Proof/attached file: </label>
                        <span><?php echo htmlspecialchars($order['proof']); ?></span><br>
                        <br>
                        <h4>


                        <a style="float: right;" href="#"><button style="color:green;" class="btn btn-success">Confirm Report</button></a><br>
                        <a style="float: right;" href="customer-billing-shipping-update.php"><button style="color: red;" class="btn btn-danger">Cancel Report</button></a><br>





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
