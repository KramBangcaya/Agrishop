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
                    <h2>Product Name:</h2>
                    <span><?php echo htmlspecialchars($order['product_name']); ?></span>

                    <div style="margin-top: 10px; font-size: medium;">
                        <label>Quantity: </label>
                        <span><?php echo htmlspecialchars($order['product_quantity']); ?></span><br>

                        <label>Total: ₱</label>
                        <span><?php echo htmlspecialchars($order['totalPayment']); ?></span><br><br>

                        <label>Payment date time: </label>
                        <span><?php echo htmlspecialchars($order['timedate']); ?></span><br>

                        <label>Transaction ID: </label>
                        <span><?php echo htmlspecialchars($order['id']); ?></span><br>

                        <label>Seller Name: </label>
                        <span><?php echo htmlspecialchars($order['seller_name']); ?></span><br>

                        <label>Seller Number: </label>
                        <span><?php echo htmlspecialchars($order['seller_number']); ?></span><br>

                        <label>Seller Address: </label>
                        <span><?php echo htmlspecialchars($order['seller_address']); ?></span><br>

                        <label>Expected Delivery: </label>
                        <span>1-2 days</span><br>

                        <label>Order Status: </label>
                        <?php if ($order['order_status'] === "Delivered"): ?>
                            <span class="badge bg-danger w-100" style="background-color:green;">Delivered</span>
                        <?php elseif ($order['order_status'] === "For Delivery"): ?>
                            <span class="badge bg-danger w-100" style="background-color:gray;">For Delivery</span>
                        <?php else: ?>
                            <span class="badge bg-danger w-100" style="background-color:red;">Pending</span>
                        <?php endif; ?>
                        <br>

                        <h4>
                            <a onclick="return confirmDelte();" href="cart-item-delete.php?id=<?php echo $order['id']; ?>" class="trash">
                                Cancel Order <i class="fa fa-ban" style="color:red;"></i>
                            </a>
                        </h4>
                        <h4 onclick="toggleFeedbackForm(event)" style="cursor: pointer;">
                            Feedback <i class="fa fa-comments" style="color:green;"></i>
                        </h4>
                        <div id="feedback-form" style="display: none; margin-top: 10px;">
                            <textarea id="feedback-text" placeholder="Enter your feedback here..." rows="4" cols="50"></textarea>
                            <br>
                            <label for="rating">Rate your experience (1-5): </label>
                            <select id="rating">
                                <option value="1">1 - Very Poor</option>
                                <option value="2">2 - Poor</option>
                                <option value="3">3 - Average</option>
                                <option value="4">4 - Good</option>
                                <option value="5">5 - Excellent</option>
                            </select>
                            <br><br>
                            <button onclick="submitFeedback()">Submit</button>
                            <button onclick="cancelFeedback()">Cancel</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
                    <script>
                    function toggleFeedbackForm(event) {
                        event.preventDefault(); // Prevent default behavior
                        const feedbackForm = document.getElementById('feedback-form');
                        if (feedbackForm) {
                            feedbackForm.style.display = feedbackForm.style.display === 'none' ? 'block' : 'none';
                        } else {
                            console.error('Feedback form not found.');
                        }
                    }

                    function submitFeedback() {
                        const textarea = document.getElementById('feedback-text');
                        const ratingSelect = document.getElementById('rating');
                        if (textarea && ratingSelect) {
                            const feedback = textarea.value.trim();
                            const rating = ratingSelect.value;
                            if (feedback) {
                                console.log('Feedback:', feedback);
                                console.log('Rating:', rating);
                                alert(`Feedback submitted: ${feedback}\nRating: ${rating}/5`);
                                document.getElementById('feedback-form').style.display = 'none'; // Hide the form after submission
                            } else {
                                alert('Please enter your feedback before submitting.');
                            }
                        } else {
                            console.error('Feedback textarea or rating select not found.');
                        }
                    }

                    function cancelFeedback() {
                        const feedbackForm = document.getElementById('feedback-form');
                        if (feedbackForm) {
                            feedbackForm.style.display = 'none'; // Hide the feedback form
                        } else {
                            console.error('Feedback form not found.');
                        }
                    }
                    </script>


                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require_once('footer.php'); ?>
