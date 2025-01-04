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


    // Get the email from the session
    $cust_email = $_SESSION['customer']['email'];

    // API URL with the email as a parameter
    $api_url = API_BASE_URL . "/login/submit?email=" . urlencode($cust_email);

    // Initialize cURL session
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification if needed
    $api_response = curl_exec($ch);
    curl_close($ch);

    // Decode the API response
    $response_data = json_decode($api_response, true);

    if ($response_data && isset($response_data['user'][0])) {
        // Get the user's data from the API response
        $user = $response_data['user'][0]; // Assuming 'status' field exists in the API response


    } else {
        // If no data is returned from the API, force logout or show an error
        header('location: '.BASE_URL.'logout.php');
        exit;
    }
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
            <h2>Productname:
                &nbsp;Product Price
                    ₱&nbsp;

                </h2>


                <!-- Product Image -->
            <!-- Responsive and spaced -->

                <!-- Quantity and Total -->
                <div style="margin-top: 10px; font-size: medium;">
                <label>Quantity:  </label>


<label>Total: </label>

₱<br><br>
<label>Payment date time: </label><br>
<label>Transaction ID: 0024903AFJE91</label><br>

<label>Seller Name: </label><br>
<label>Seller Number:</label><br>
<label>Seller Address: </label><br>
<label>Expected Delivery: 1-2 days</label><br>
<label>Order Status:
<?php if ("Delivered" == "Delivered") { ?>
<span class="badge bg-danger w-100" style="background-color:green;">Delivered</span>
<?php } elseif ("For Delivery" == "For Delivery") { ?>
<span class="badge bg-danger w-100" style="background-color:gray;">For Delivery</span>
<?php } else { ?>
<span class="badge bg-danger w-100" style="background-color:red;">Pending</label></span>
<?php } ?>

</label>
<br>
<h4 onclick="toggleCancelOrderForm(event)" style="cursor: pointer;">
    Cancel Order <i class="fa fa-ban" style="color:red;"></i>
</h4>
<div id="cancel-order-form" style="display: none; margin-top: 10px;">
    <textarea id="cancel-reason" placeholder="Enter the reason for canceling the order..." rows="4" cols="50"></textarea>
    <br>
    <button onclick="submitCancelOrder()">Submit</button>
    <button onclick="cancelCancelOrder()">Cancel</button>
</div>


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

function toggleCancelOrderForm(event) {
    event.preventDefault(); // Prevent default behavior
    const cancelOrderForm = document.getElementById('cancel-order-form');
    if (cancelOrderForm) {
        cancelOrderForm.style.display = cancelOrderForm.style.display === 'none' ? 'block' : 'none';
    } else {
        console.error('Cancel order form not found.');
    }
}
function submitCancelOrder() {
    const textarea = document.getElementById('cancel-reason');
    if (textarea) {
        const reason = textarea.value.trim();
        if (reason) {
            console.log('Cancel reason:', reason);
            alert(`Cancellation reason submitted: ${reason}`);
            // Add your AJAX call or backend integration here
            document.getElementById('cancel-order-form').style.display = 'none'; // Hide the form after submission
        } else {
            alert("Please enter a reason before submitting.");
        }
    } else {
        console.error('Cancel reason textarea not found.');
    }
}
function cancelCancelOrder() {
    const cancelOrderForm = document.getElementById('cancel-order-form');
    if (cancelOrderForm) {
        cancelOrderForm.style.display = 'none'; // Hide the cancel order form
    } else {
        console.error('Cancel order form not found.');
    }
}
</script>


                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>
